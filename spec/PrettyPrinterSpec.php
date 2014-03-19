<?php

use Tusk\PrettyPrinter;

describe('PrettyPrinter', function() {
    beforeEach(function() {
        $this->prettyPrinter = new PrettyPrinter();
    });

    describe('format()', function() {
        it('should replace placeholders with the numbered arguments', function() {
            expect($this->prettyPrinter->format('to be {1} {2} {0}', 12.4, [1, 2, 3]))->toBe(
                'Expected 12.4 to be 2 3 1'
            );
        });

        it('should include the word "not" if comparison is inverted', function() {
            expect($this->prettyPrinter->format('to be {1} {2} {0}', 12.4, [1, 2, 3], true))->toBe(
                'Expected 12.4 not to be 2 3 1'
            );
        });

        it('should surround string arguments with single quotes', function() {
            expect($this->prettyPrinter->format('to be {0}', 'foo', ['bar']))->toBe(
                "Expected 'foo' to be 'bar'"
            );
        });

        it('should escape quotes within string arguments', function() {
            expect($this->prettyPrinter->format('to be {0}', "'foo'", ["b'a'r"]))->toBe(
                "Expected '\\'foo\\'' to be 'b\\'a\\'r'"
            );
        });

        it('should display strings "true" and "false" for boolean values', function() {
            expect($this->prettyPrinter->format('to be {0}', true, [false]))->toBe(
                'Expected true to be false'
            );
        });

        it('should display arrays in easy to read format', function() {
            $output = "Expected [0 => 'a', 1 => 'b', 2 => 'c'] to be ['x' => 1, 'y' => 2, 'z' => 3]";

            expect(
                $this->prettyPrinter->format(
                    'to be {0}',
                    ['a', 'b', 'c'],
                    [['x' => 1, 'y' => 2, 'z' => 3]]
                )
            )->toBe($output);
        });

        it('should call __toString() on object if available', function() {
            $object = new \SplFileInfo('php://output');

            expect($this->prettyPrinter->format('to be {0}', $object, [$object]))->toBe(
                'Expected php://output to be php://output'
            );
        });

        it('should display class name if object cannot be converted to string', function() {
            $object = new \stdClass();

            expect($this->prettyPrinter->format('to be {0}', $object, [$object]))->toBe(
                'Expected <stdClass> to be <stdClass>'
            );
        });

        it('should display something sensible for resources', function() {
            $resource = fopen('php://output', 'w');

            expect($this->prettyPrinter->format('to be {0}', $resource, [$resource]))->toMatch(
                '/^Expected Resource id #\d+ to be Resource id #\d+$/'
            );
        });
    });
});
