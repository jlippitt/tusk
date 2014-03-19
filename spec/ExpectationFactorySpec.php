<?php

use Mockery as m;
use Tusk\Expectation;
use Tusk\ExpectationFactory;

describe('ExpectationFactory', function() {
    beforeEach(function() {
        $this->prettyPrinter = m::mock('Tusk\PrettyPrinter');

        $this->factory = new ExpectationFactory($this->prettyPrinter);
    });

    afterEach(function() {
        m::close();
    });

    describe('createExpectation()', function() {
        it('should create an expectation with the matchers added in addMatcher()', function() {
            $value = 'foo';

            $matchers = [
                'matcher1' => m::mock('Tusk\Matcher'),
                'matcher2' => m::mock('Tusk\Matcher')
            ];

            $context = m::mock('Tusk\AbstractContext');

            foreach ($matchers as $key => $value) {
                $this->factory->addMatcher($key, $value);
            }

            $expectation = $this->factory->createExpectation($value);

            expect($expectation)->toBeInstanceOf('Tusk\Expectation');

            expect($expectation)->toEqual(
                new Expectation(
                    $value,
                    $matchers,
                    $this->prettyPrinter
                )
            );
        });
    });
});
