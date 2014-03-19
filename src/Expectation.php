<?php

namespace Tusk;

class Expectation
{
    private $value;

    private $comparators;

    private $prettyPrinter;

    public function __construct(
        $value,
        array $comparators,
        PrettyPrinter $prettyPrinter
    ) {
        $this->value = $value;
        $this->comparators = $comparators;
        $this->prettyPrinter = $prettyPrinter;
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

            if ($comparator->compare($this->value, $args) === $inverted) {
                throw new ExpectationException(
                    $this->prettyPrinter->format(
                        $comparator->getMessageFormat(),
                        $this->value,
                        $args,
                        $inverted
                    )
                );
            }

        } else {
            throw new \BadMethodCallException(
                "Method '{$method}' does not map to a known comparator"
            );
        }
    }
}
