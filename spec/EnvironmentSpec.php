<?php

use Mockery as m;
use Tusk\Environment;

describe('Environment', function() {
    beforeEach(function() {
        $this->env = new Environment();
    });

    afterEach(function() {
        m::close();
    });

    describe('execute()', function() {
        it('should push the context onto the stack, execute it, then pop it', function() {
            $outerContext = m::mock('Tusk\AbstractContext');
            $innerContext = m::mock('Tusk\AbstractContext');

            $innerContextCalled = false;

            $outerContext
                ->shouldReceive('execute')
                ->with(false)
                ->once()
                ->andReturnUsing(function() use ($outerContext, $innerContext) {
                    expect($this->env->getContext())->toBe($outerContext);
                    $this->env->execute($innerContext);
                    expect($this->env->getContext())->toBe($outerContext);
                })
            ;

            $innerContext
                ->shouldReceive('execute')
                ->with(false)
                ->once()
                ->andReturnUsing(function() use ($innerContext, &$innerContextCalled) {
                    expect($this->env->getContext())->toBe($innerContext);
                    $innerContextCalled = true;
                })
            ;

            expect($this->env->getContext())->toBe(null);

            $this->env->execute($outerContext);

            expect($this->env->getContext())->toBe(null);
            expect($innerContextCalled)->toBe(true);
        });

        it('pass "true" to execute() if called within a skip() block', function() {
            $outerContext = m::mock('Tusk\AbstractContext');
            $innerContext = m::mock('Tusk\AbstractContext');

            $innerContextCalled = false;

            $outerContext
                ->shouldReceive('execute')
                ->with(true)
                ->once()
                ->andReturnUsing(function() use ($outerContext, $innerContext) {
                    expect($this->env->getContext())->toBe($outerContext);
                    $this->env->execute($innerContext);
                    expect($this->env->getContext())->toBe($outerContext);
                })
            ;

            $innerContext
                ->shouldReceive('execute')
                ->with(true)
                ->once()
                ->andReturnUsing(function() use ($innerContext, &$innerContextCalled) {
                    expect($this->env->getContext())->toBe($innerContext);
                    $innerContextCalled = true;
                })
            ;

            expect($this->env->getContext())->toBe(null);

            $this->env->skip(function() use ($outerContext) {
                $this->env->execute($outerContext);
            });

            expect($this->env->getContext())->toBe(null);
            expect($innerContextCalled)->toBe(true);
        });
    });
});
