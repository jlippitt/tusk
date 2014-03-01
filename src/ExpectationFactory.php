<?php

namespace Tusk;

class ExpectationFactory
{
    private $env;

    private $comparators = array();

    public function __construct(Environment $env)
    {
        $this->env = $env;
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
            $this->env->getContext()
        );
    }
}
