<?php

namespace Tusk;

class ExpectationFactory
{
    private $comparators = array();

    public function addComparator($name, Comparator $comparator)
    {
        $this->comparators[$name] = $comparator;
    }

    public function createExpectation($value, AbstractContext $context)
    {
        return new Expectation($value, $this->comparators, $context);
    }
}
