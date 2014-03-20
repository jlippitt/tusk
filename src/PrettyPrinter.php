<?php

namespace Tusk;

/**
 * Formats expectation failure messages, displaying variables in a human-
 * readable way.
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class PrettyPrinter
{
    /**
     * Formats an expectation failure message using the supplied parameters
     *
     * @param string $format Format string. Can contain references to $args
     * by index, e.g. '{0}', '{1}', etc.
     * @param mixed $value Value being tested
     * @param mixed[] $args Arguments that were passed to matcher
     * @param bool $inverted If true, formatted output will contain the word
     * 'not' to negate its meaning
     */
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

        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';

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
