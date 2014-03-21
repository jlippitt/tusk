<?php

namespace Tusk;

use Mockery as m;
use Tusk\Spec;

describe('Spec', function() {
    afterEach(function() {
        m::close();
    });

    describe('setUp()', function() {
        it('should add the spec to the runner', function() {
            $specRunner = m::mock('Tusk\SpecRunner');

            $spec = new Spec(
                'ignore',
                function() {},
                m::mock('Tusk\AbstractContext'),
                $specRunner
            );

            $specRunner->shouldReceive('add')->with($spec)->once();

            $spec->setUp();
        });
    });

    describe('run()', function() {
        it('should run hooks and body, bound to clone of parent scope', function() {
            $parentScope = new \stdClass();
            $parentScope->a = 1;

            $parent = m::mock(
                'Tusk\Suite',
                array(
                    'getScope' => $parentScope
                )
            );

            // Precisely check the order in which things occur:
            // 1. Pre hooks
            // 2. Body
            // 3. Post hooks

            $preHooksExecuted = false;
            $postHooksExecuted = false;

            $executePreHooks = function ($scope) use (
                $parentScope,
                &$preHooksExecuted
            ) {
                expect($scope)->toEqual($parentScope);
                expect($scope)->notToBe($parentScope);
                $preHooksExecuted = true;
            };

            $parent
                ->shouldReceive('executePreHooks')
                ->with(m::type('object'))
                ->once()
                ->andReturnUsing($executePreHooks)
            ;

            $body = function () use (
                $parentScope,
                &$preHooksExecuted,
                &$postHooksExecuted
            ) {
                expect($this)->toEqual($parentScope);
                expect($this)->notToBe($parentScope);
                expect($preHooksExecuted)->toBe(true);
                expect($postHooksExecuted)->toBe(false);
            };

            $executePostHooks = function ($scope) use (
                $parentScope,
                &$postHooksExecuted
            ) {
                expect($scope)->toEqual($parentScope);
                expect($scope)->notToBe($parentScope);
                $postHooksExecuted = true;
            };

            $parent
                ->shouldReceive('executePostHooks')
                ->with(m::type('object'))
                ->once()
                ->andReturnUsing($executePostHooks)
            ;

            $spec = new Spec(
                'ignore',
                $body,
                $parent,
                m::mock('Tusk\SpecRunner')
            );

            $spec->run();
        });

        it('should throw an exception if body fails', function() {
            $parent = m::mock(
                'Tusk\Suite',
                [
                    'getScope' => new \stdClass(),
                    'executePreHooks' => null,
                    'executePostHooks' => null
                ]
            );

            $spec = new Spec(
                'ignore',
                function() { throw new \Exception('body error'); },
                $parent,
                m::mock('Tusk\SpecRunner')
            );

            expect(function() use ($spec) { $spec->run(); })->toThrow(
                'Exception',
                'body error'
            );
        });

        it('should throw an exception if pre-hook fails', function() {
            $parent = m::mock(
                'Tusk\Suite',
                [
                    'getScope' => new \stdClass(),
                    'executePostHooks' => null
                ]
            );

            $parent->shouldReceive('executePreHooks')->andThrow(
                new \Exception('pre-hook error')
            );

            $spec = new Spec(
                'ignore',
                function() {},
                $parent,
                m::mock('Tusk\SpecRunner')
            );

            expect(function() use ($spec) { $spec->run(); })->toThrow(
                'Exception',
                'pre-hook error'
            );
        });

        it('should throw an exception if post-hook fails', function() {
            $parent = m::mock(
                'Tusk\Suite',
                [
                    'getScope' => new \stdClass(),
                    'executePreHooks' => null
                ]
            );

            $parent->shouldReceive('executePostHooks')->andThrow(
                new \Exception('post-hook error')
            );

            $spec = new Spec(
                'ignore',
                function() {},
                $parent,
                m::mock('Tusk\SpecRunner')
            );

            expect(function() use ($spec) { $spec->run(); })->toThrow(
                'Exception',
                'post-hook error'
            );
        });

        it('should attempt to execute post-hooks even if an exception is thrown by the body', function() {
            $parent = m::mock(
                'Tusk\Suite',
                ['getScope' => new \stdClass(), 'executePreHooks' => null]
            );

            $parent->shouldReceive('executePostHooks')->once();

            $spec = new Spec(
                'ignore',
                function() { throw new \Exception('body error'); },
                $parent,
                m::mock('Tusk\SpecRunner')
            );

            expect(function() use ($spec) { $spec->run(); })->toThrow(
                'Exception',
                'body error'
            );
        });

        it('should not attempt to execute the body or post-hooks if an exception is thrown by a pre-hook', function() {
            $parent = m::mock(
                'Tusk\Suite',
                ['getScope' => new \stdClass()]
            );

            $parent->shouldReceive('executePreHooks')->andThrow(
                new \Exception('pre-hook error')
            );

            $parent->shouldReceive('executePostHooks')->never();

            $bodyCalled = false;

            $spec = new Spec(
                'ignore',
                function() use (&$bodyCalled) { $bodyCalled = true; },
                $parent,
                m::mock('Tusk\SpecRunner')
            );

            expect(function() use ($spec) { $spec->run(); })->toThrow(
                'Exception',
                'pre-hook error'
            );

            expect($bodyCalled)->toBe(false);
        });

        it('should throw the exception from the body if a post-hook throws an exception as well', function() {
            $parent = m::mock(
                'Tusk\Suite',
                ['getScope' => new \stdClass(), 'executePreHooks' => null]
            );

            $parent->shouldReceive('executePostHooks')->andThrow(
                new \Exception('post-hook error')
            );

            $spec = new Spec(
                'ignore',
                function() { throw new \Exception('body error'); },
                $parent,
                m::mock('Tusk\SpecRunner')
            );

            expect(function() use ($spec) { $spec->run(); })->toThrow(
                'Exception',
                'body error'
            );
        });
    });
});
