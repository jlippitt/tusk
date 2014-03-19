<?php

namespace Tusk;

class PrettyPrinter
{
    public function format($format, $value, array $args, $inverted = false)
    {
        $message = "Expected {$this->formatValue($value)} ";

        if ($inverted) {
            $message .= 'not ';
        }

        $message .= preg_replace_callback(
            '/\{(\d+)\}/',
            function ($match) use ($args) {
                return $this->formatValue($args[(int)$match[1]]);
            },
            $format
        );

        return $message;
    }

    private function formatValue($value)
    {
        if (is_string($value)) {
            $value = "'" . str_replace("'", "\\'", $value) . "'";

        } elseif (is_array($value)) {
            $elements = [];

            foreach ($value as $key => $element) {
                $elements[] = $this->formatValue($key) .
                    ' => ' . $this->formatValue($element);
            }

            $value = '[' . implode(', ', $elements) . ']';

        } elseif (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = (string)$value;
            } else {
                $value = '<' . get_class($value) . '>';
            }
        }

        return $value;
    }
}
