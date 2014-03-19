<?php

use Tusk\Comparator;

describe('Comparator', function() {
    describe('compare()', function() {
        it('should call body callback with combined value/args array', function() {
            $value = 1;

            $args = array(2, 3);

            $body = function () {
                expect(func_get_args())->toBe(array(1, 2, 3));
            };

            $comparator = new Comparator($body, 'ignore');

            $comparator->compare($value, $args);
        });
    });

    describe('getMessageFormat()', function() {
        it('should return the format string passed to the constructor', function() {
            $comparator = new Comparator(function() {}, 'format string');
            expect($comparator->getMessageFormat())->toBe('format string');
        });
    });
});
