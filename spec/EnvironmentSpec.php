<?php

use Mockery as m;
use Tusk\Environment;

describe('Environment', function() {
    beforeEach(function() {
        $this->expectationFactory = m::mock('Tusk\ExpectationFactory');

        $this->env = new Environment($this->expectationFactory);
    });

    afterEach(function() {
        m::close();
    });

    describe('setContext()', function() {
        it('should set context that can be retrieved with getContext()', function() {
            $context = m::mock('Tusk\AbstractContext');

            $this->env->setContext($context);

            expect($this->env->getContext())->toBe($context);
        });

        it('should permit passing in a null value', function() {
            $this->env->setContext(null);

            expect($this->env->getContext())->toBe(null);
        });
    });

    describe('getExpectationFactory()', function() {
        it('should return the factory passed into the constructor', function() {
            expect($this->env->getExpectationFactory())->toBe(
                $this->expectationFactory
            );
        });
    });
});
