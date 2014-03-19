<?php

use Mockery as m;
use Tusk\Expectation;

describe('Expectation', function() {
    afterEach(function() {
        m::close();
    });

    describe('__call()', function() {
        beforeEach(function() {
            $this->matcher = m::mock('Tusk\Matcher', [
                'getMessageFormat' => 'failed'
            ]);

            $this->prettyPrinter = m::mock('Tusk\PrettyPrinter');

            $this->expectation = new Expectation(
                12,
                ['toBe' => $this->matcher],
                $this->prettyPrinter
            );
        });

        it('should invoke a matcher object with the expectation value', function() {
            $this->matcher
                ->shouldReceive('match')
                ->with(12, [13, 14])
                ->andReturn(true)
            ;

            $this->expectation->toBe(13, 14);
        });

        it('should throw an exception if the comparison returns false', function() {
            $this->matcher
                ->shouldReceive('match')
                ->with(12, [15, 16])
                ->andReturn(false)
            ;

            $this->prettyPrinter
                ->shouldReceive('format')
                ->with('failed', 12, [15, 16], false)
                ->andReturn('foo')
                ->once()
            ;

            expect(function() { $this->expectation->toBe(15, 16); })->toThrow(
                'Tusk\ExpectationException',
                'foo'
            );
        });

        it('should reverse the above behaviour if matcher name is preceded by "not"', function() {
            $this->matcher
                ->shouldReceive('match')
                ->with(12, [13, 14])
                ->andReturn(false)
            ;

            $this->expectation->notToBe(13, 14);

            $this->matcher
                ->shouldReceive('match')
                ->with(12, [15, 16])
                ->andReturn(true)
            ;

            $this->prettyPrinter
                ->shouldReceive('format')
                ->with('failed', 12, [15, 16], true)
                ->andReturn('bar')
                ->once()
            ;

            expect(function() { $this->expectation->notToBe(15, 16); })->toThrow(
                'Tusk\ExpectationException',
                'bar'
            );
        });

        it('should throw an exception if the matcher does not exist', function() {
            expect(function() { $this->expectation->notToExist(13, 14); })->toThrow('BadMethodCallException');
        });
    });
});
