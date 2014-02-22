<?php

namespace Tusk;

class Expectation
{
    private $value;

    private $context;

    public function __construct($value, Context $context)
    {
        $this->value = $value;
        $this->context = $context;
    }

    public function toBe($expected)
    {
        if ($this->value !== $expected) {
            throw new ExpectationException(
                "Expected '{$expected}' but got '{$this->value}'",
                $this->context
            );
        }
    }
}
