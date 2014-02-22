<?php

namespace Tusk;

class Comparator
{
    private $body;

    private $message;

    public function __construct(callable $body, $message)
    {
        $this->body = $body;
        $this->message = $message;
    }

    public function __invoke($value, array $args)
    {
        array_unshift($args, $value);

        return call_user_func_array($this->body, $args);
    }

    public function formatMessage($value, array $args, $inverted = false)
    {
        $message = "Expected {$value} ";

        if ($inverted) {
            $message .= 'not ';
        }

        $message .= preg_replace_callback(
            '/\{(\d+)\}/',
            function ($match) use ($args) {
                return $args[(int)$match[1]];
            },
            $this->message
        );

        return $message;
    }
}
