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

    public function __invoke(array $args)
    {
        return $this->body->__invoke($args);
    }

    public function formatMessage(array $args, $inverted = false)
    {
        return preg_replace_callback(
            '/\{(\d+)\}/',
            function ($match) use ($args) {
                return $args[(int)$match[1]];
            },
            preg_replace(
                '/(\s+)\[not\](\s+)/',
                $inverted ? '\1not\2' : ' ',
                $this->message
            )
        );
    }
}
