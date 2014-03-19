<?php

namespace Tusk;

class ExpectationFactory
{
    private $prettyPrinter;

    private $comparators = array();

    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->prettyPrinter = $prettyPrinter;
    }

    public function addComparator($name, Comparator $comparator)
    {
        $this->comparators[$name] = $comparator;
    }

    public function createExpectation($value)
    {
        return new Expectation(
            $value,
            $this->comparators,
            $this->prettyPrinter
        );
    }
}
