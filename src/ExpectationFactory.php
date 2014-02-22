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

        $this->addComparator(
            'toBe',
            new Comparator(
                function ($value, $expected) {
                    return $value === $expected;
                },
                "Expected {0} [not] to be {1}"
            )
        );
    }

    public function addComparator($name, Comparator $comparator)
    {
        $this->comparators[$name] = $comparator;
    }

    public function createExpectation($value, AbstractContext $context)
    {
        return new Expectation($value, $this->comparators, $context);
    }
}
