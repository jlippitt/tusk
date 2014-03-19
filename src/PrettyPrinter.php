<?php

namespace Tusk;

class PrettyPrinter
{
    public function format($format, $value, array $args, $inverted = false)
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
            $format
        );

        return $message;
    }
}
