<?php

use Tusk\Matcher;

describe('Matcher', function() {
    describe('compare()', function() {
        it('should call body callback with combined value/args array', function() {
            $value = 1;

            $args = array(2, 3);

            $body = function () {
                expect(func_get_args())->toBe(array(1, 2, 3));
            };

            $matcher = new Matcher($body, 'ignore');

            $matcher->compare($value, $args);
        });
    });

    describe('getMessageFormat()', function() {
        it('should return the format string passed to the constructor', function() {
            $matcher = new Matcher(function() {}, 'format string');
            expect($matcher->getMessageFormat())->toBe('format string');
        });
    });
});
