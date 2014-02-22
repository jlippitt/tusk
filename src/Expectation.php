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
        if (substr($method, 0, 3) === 'not') {
            $method = lcfirst(substr($method, 3));
            $inverted = true;

        } else {
            $inverted = false;
        }

        if (array_key_exists($method, $this->comparators)) {
            $comparator = $this->comparators[$method];

            if ($comparator($this->value, $args) === $inverted) {
                throw new ExpectationException(
                    $comparator->formatMessage($this->value, $args, $inverted),
                    $this->context
                );
            }

        } else {
            throw new \BadMethodCallException(
                "Method '{$method}' does not map to a known comparator"
            );
        }
    }
}
