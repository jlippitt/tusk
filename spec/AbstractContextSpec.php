<?php

use Mockery as m;

describe('AbstractContext', function() {
    afterEach(function() {
        m::close();
    });

    describe('getDescription()', function() {
        it('should return the passed description if no parent is set', function() {
            $context = m::mock(
                'Tusk\AbstractContext[setUp]',
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
                'Tusk\AbstractContext[setUp]',
                ['getTrunk()', $parent]
            );

            expect($context->getDescription())->toBe('Elephant getTrunk()');
        });
    });

    describe('isSkipped()', function() {
        it('should return false if the current context is not marked as "skipped"', function() {
            $context = m::mock(
                'Tusk\AbstractContext[setUp]',
                ['getTrunk']
            );

            expect($context->isSkipped())->toBe(false);

            $parent = m::mock('Tusk\AbstractContext', ['isSkipped' => false]);

            $context = m::mock(
                'Tusk\AbstractContext[setUp]',
                ['getTrunk', $parent]
            );

            expect($context->isSkipped())->toBe(false);
        });

        it('should return true if the current context is marked as "skipped"', function() {
            $context = m::mock(
                'Tusk\AbstractContext[setUp]',
                ['getTrunk', null, true]
            );

            expect($context->isSkipped())->toBe(true);

            $parent = m::mock('Tusk\AbstractContext', ['isSkipped' => false]);

            $context = m::mock(
                'Tusk\AbstractContext[setUp]',
                ['getTrunk', $parent, true]
            );

            expect($context->isSkipped())->toBe(true);
        });

        it('should return true if any parent context is marked as "skipped"', function() {
            $parent = m::mock('Tusk\AbstractContext', ['isSkipped' => true]);

            $context = m::mock(
                'Tusk\AbstractContext[setUp]',
                ['getTrunk', $parent]
            );

            expect($context->isSkipped())->toBe(true);
        });
    });
});
