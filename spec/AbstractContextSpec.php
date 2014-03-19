<?php

use Mockery as m;

describe('AbstractContext', function() {
    afterEach(function() {
        m::close();
    });

    describe('getDescription()', function() {
        it('should return the passed description if no parent is set', function() {
            $context = m::mock(
                'Tusk\AbstractContext[execute]',
                ['getTrunk()']
            );

            expect($context->getDescription())->toBe('getTrunk()');
        });

        it('should append to parent description if parent is set', function() {
            $parent = m::mock(
                'Tusk\AbstractContext',
                ['getDescription' => 'Elephant']
            );

            $context = m::mock(
                'Tusk\AbstractContext[execute]',
                ['getTrunk()', $parent]
            );

            expect($context->getDescription())->toBe('Elephant getTrunk()');
        });
    });
});
