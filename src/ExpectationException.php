<?php

namespace Tusk;

class ExpectationException extends \Exception
{
    public function __construct($text, Context $context)
    {
        parent::__construct(
            "Expectation failed for '{$context->getDescription()}': {$text}"
        );
    }
}
