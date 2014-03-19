<?php

namespace Tusk;

class Expectation
{
    private $value;

    private $matchers;

    private $prettyPrinter;

    public function __construct(
        $value,
        array $matchers,
        PrettyPrinter $prettyPrinter
    ) {
        $this->value = $value;
        $this->matchers = $matchers;
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

        if (array_key_exists($method, $this->matchers)) {
            $matcher = $this->matchers[$method];

            if ($matcher->compare($this->value, $args) === $inverted) {
                throw new ExpectationException(
                    $this->prettyPrinter->format(
                        $matcher->getMessageFormat(),
                        $this->value,
                        $args,
                        $inverted
                    )
                );
            }

        } else {
            throw new \BadMethodCallException(
                "Method '{$method}' does not map to a known matcher"
            );
        }
    }
}
