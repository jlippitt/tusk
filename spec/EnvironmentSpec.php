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
});
