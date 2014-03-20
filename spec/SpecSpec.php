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
                m::mock('Tusk\Scoreboard', ['pass' => null])
            );

            $spec->execute();
        });

        describe('scoring', function() {
            beforeEach(function() {
                $this->scoreboard = m::mock('Tusk\Scoreboard');

                $this->parent = m::mock('Tusk\Suite', [
                    'getDescription' => 'spec',
                    'getScope' => new \stdClass(),
                    'executePreHooks' => null,
                    'executePostHooks' => null
                ]);

                $this->exception = null;

                $exception = &$this->exception;

                $body = function() use (&$exception) {
                    if ($exception !== null) {
                        throw $exception;
                    }
                };

                $this->spec = new Spec(
                    'description',
                    $body,
                    $this->parent,
                    $this->scoreboard
                );
            });

            it('should pass the spec if no exceptions are thrown', function() {
                $this->scoreboard
                    ->shouldReceive('pass')
                    ->once()
                ;

                $this->spec->execute();
            });

            it('should fail the spec if body throws an exception', function() {
                $this->exception = new \Exception('body broke');

                $this->scoreboard
                    ->shouldReceive('fail')
                    ->with('spec description', 'body broke')
                    ->once()
                ;

                $this->spec->execute();
            });

            it('should fail the spec if pre-hook throws an exception', function() {
                $this->parent
                    ->shouldReceive('executePreHooks')
                    ->andThrow(new \Exception('pre-hook broke'))
                ;

                $this->scoreboard
                    ->shouldReceive('fail')
                    ->with('spec description', 'pre-hook broke')
                    ->once()
                ;

                $this->spec->execute();
            });

            it('should fail the spec if post-hook throws an exception', function() {
                $this->parent
                    ->shouldReceive('executePostHooks')
                    ->andThrow(new \Exception('post-hook broke'))
                ;

                $this->scoreboard
                    ->shouldReceive('fail')
                    ->with('spec description', 'post-hook broke')
                    ->once()
                ;

                $this->spec->execute();
            });

            it('should mark the spec as skipped if the skip flag is set on the contextStackironment', function() {
                $this->scoreboard
                    ->shouldReceive('skip')
                    ->once()
                ;

                $this->spec->execute(true);
            });
        });
    });
});
