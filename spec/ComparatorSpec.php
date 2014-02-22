<?php

use Tusk\Comparator;

describe('Comparator', function() {
    describe('__invoke()', function() {
        it('should call body callback with combined value/args array', function() {
            $value = 1;

            $args = array(2, 3);

            $body = function () {
                expect(func_get_args())->toBe(array(1, 2, 3));
            };

            $comparator = new Comparator($body, 'ignore');

            $comparator($value, $args);
        });
    });

    describe('formatMessage()', function() {
        it('should replace placeholders with the numbered arguments', function() {
            $value = 12;

            $args = array(1, 2, 3);

            $comparator = new Comparator(function() {}, 'to be {1} {2} {0}');

            expect($comparator->formatMessage($value, $args))->toBe(
                'Expected 12 to be 2 3 1'
            );
        });

        it('should include the word "not" if comparison is inverted', function() {
            $value = 12;

            $args = array(12);

            $comparator = new Comparator(function() {}, 'to be {0}');

            expect($comparator->formatMessage($value, $args, true))->toBe(
                'Expected 12 not to be 12'
            );
        });
    });
});
