<?php

use Tusk\PrettyPrinter;

describe('PrettyPrinter', function() {
    beforeEach(function() {
        $this->prettyPrinter = new PrettyPrinter();
    });

    describe('format()', function() {
        it('should replace placeholders with the numbered arguments', function() {
            expect($this->prettyPrinter->format('to be {1} {2} {0}', 12, [1, 2, 3]))->toBe(
                'Expected 12 to be 2 3 1'
            );
        });

        it('should include the word "not" if comparison is inverted', function() {
            expect($this->prettyPrinter->format('to be {1} {2} {0}', 12, [1, 2, 3], true))->toBe(
                'Expected 12 not to be 2 3 1'
            );
        });
    });
});
