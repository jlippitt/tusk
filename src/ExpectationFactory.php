<?php

namespace Tusk;

class ExpectationFactory
{
    private $comparators = array();

    public function __construct()
    {
        /**
         * Add built-in comparators
         */

        $this->addComparator('toBe', function ($value, $expected) {
            return $value === $expected;
        });
    }

    public function addComparator($name, \Closure $body)
    {
        $this->comparators[$name] = $body;
    }

    public function createExpectation($value, AbstractContext $context)
    {
        return new Expectation($value, $this->comparators, $context);
    }
}
