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

            $spec = new Spec('ignore', $body, $env);
            $spec->execute();
        });
    });
});
