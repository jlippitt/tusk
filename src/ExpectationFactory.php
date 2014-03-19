<?php

namespace Tusk;

class ExpectationFactory
{
    private $prettyPrinter;

    private $matchers = array();

    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->prettyPrinter = $prettyPrinter;
    }

    public function addMatcher($name, Matcher $matcher)
    {
        $this->matchers[$name] = $matcher;
    }

    public function createExpectation($value)
    {
        return new Expectation(
            $value,
            $this->matchers,
            $this->prettyPrinter
        );
    }
}
