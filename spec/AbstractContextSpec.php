<?php

use Mockery as m;

describe('AbstractContext', function() {
    beforeEach(function() {
        $this->parent = m::mock('Tusk\AbstractContext');

        $this->env = m::mock(
            'Tusk\Environment',
            array(
                'getContext' => $this->parent,
                'setContext' => ''
            )
        );

        $this->context = m::mock(
            'Tusk\AbstractContext[executeBody]',
            array('getTrunk', $this->env)
        );

        $this->context->shouldAllowMockingProtectedMethods();
    });

    afterEach(function() {
        m::close();
    });

    describe('execute', function() {
        it('should call executeBody after setting context and return context to its prior state', function() {
            $this->env
                ->shouldReceive('setContext')
                ->with($this->context)
                ->globally()
                ->ordered()
            ;

            $this->context
                ->shouldReceive('executeBody')
                ->globally()
                ->ordered()
            ;

            $this->env
                ->shouldReceive('setContext')
                ->with($this->parent)
                ->globally()
                ->ordered()
            ;

            $this->context->execute();
        });
    });

    describe('getDescription', function() {
        it('should return the passed description if no parent is set', function() {
            expect($this->context->getDescription())->toBe('getTrunk');
        });

        it('should append to parent description if parent is set', function() {
            $this->parent
                ->shouldReceive('getDescription')
                ->andReturn('Elephant')
            ;

            $executeBody = function () {
                expect($this->context->getDescription())->toBe('Elephant getTrunk');
            };

            $this->context
                ->shouldReceive('executeBody')
                ->andReturnUsing($executeBody->bindTo($this, $this))
            ;

            $this->context->execute();
        });
    });
});
