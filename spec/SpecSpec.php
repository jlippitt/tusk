<?php

namespace Tusk;

use Mockery as m;
use Tusk\Spec;

describe('Spec', function() {
    afterEach(function() {
        m::close();
    });

    describe('execute()', function() {
        it('should execute hooks and body, bound to clone of parent scope', function() {
            $parentScope = new \stdClass();
            $parentScope->a = 1;

            $parent = m::mock(
                'Tusk\Suite',
                array(
                    'getScope' => $parentScope
                )
            );

            $env = m::mock(
                'Tusk\Environment',
                array(
                    'getContext' => $parent,
                    'setContext' => ''
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

            $spec = new Spec('ignore', $body, $env, m::mock('Tusk\Scoreboard', ['pass' => null]));
            $spec->execute();
        });

        describe('scoring', function() {
            beforeEach(function() {
                $this->scoreboard = m::mock('Tusk\Scoreboard');

                $this->parent = m::mock('Tusk\Suite', [
                    'getScope' => new \stdClass(),
                    'executePreHooks' => null,
                    'executePostHooks' => null
                ]);
            });

            afterEach(function() {
                $env = m::mock('Tusk\Environment', [
                    'getContext' => $this->parent,
                    'setContext' => null
                ]);

                $spec = new Spec(
                    'ignore',
                    $this->body,
                    $env,
                    $this->scoreboard
                );

                $spec->execute();
            });

            it('should increment passed specs if no exceptions are thrown', function() {
                $this->body = function() {};

                $this->scoreboard
                    ->shouldReceive('pass')
                    ->once()
                ;

            });

            it('should increment failed specs if body throws an exception', function() {
                $this->body = function () {
                    throw new \Exception('blah');
                };

                $this->scoreboard
                    ->shouldReceive('fail')
                    ->once()
                ;
            });

            it('should increment failed specs if pre-hook throws an exception', function() {
                $this->body = function () {};

                $this->parent
                    ->shouldReceive('executePreHooks')
                    ->andThrow(new \Exception('blah'))
                ;

                $this->scoreboard
                    ->shouldReceive('fail')
                    ->once()
                ;
            });

            it('should increment failed specs if post-hook throws an exception', function() {
                $this->body = function () {};

                $this->parent
                    ->shouldReceive('executePostHooks')
                    ->andThrow(new \Exception('blah'))
                ;

                $this->scoreboard
                    ->shouldReceive('fail')
                    ->once()
                ;
            });
        });
    });
});
