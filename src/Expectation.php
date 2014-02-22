<?php

namespace Tusk;

class Expectation
{
    private $value;

    private $comparators;

    private $context;

    public function __construct(
        $value,
        array $comparators,
        AbstractContext $context
    ) {
        $this->value = $value;
        $this->comparators = $comparators;
        $this->context = $context;
    }

    public function __call($method, array $args)
    {
        array_unshift($args, $this->value);

        if (!call_user_func_array($this->comparators[$method], $args)) {
            throw new ExpectationException(
                "Expected '{$expected}' but got '{$this->value}'",
                $this->context
            );
        }
    }
}
