<?php

namespace Tusk;

class Matcher
{
    private $body;

    private $messageFormat;

    public function __construct(callable $body, $messageFormat)
    {
        $this->body = $body;
        $this->messageFormat = $messageFormat;
    }

    public function match($value, array $args)
    {
        array_unshift($args, $value);

        return call_user_func_array($this->body, $args);
    }

    public function getMessageFormat()
    {
        return $this->messageFormat;
    }
}
