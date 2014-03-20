<?php

namespace Tusk;

/**
 * Class for a matcher to be used in an Expectation
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Matcher
{
    private $body;

    private $messageFormat;

    /**
     * @param callable $body Function that can take any number of arguments and
     * returns true if everything is OK. First argument is always the value
     * being tested.
     * @param string $messageFormat Format of message to be displayed in the
     * event of a mismatch. May contain references to function arguments in
     * format of '{0}', '{1}', etc.
     */
    public function __construct(callable $body, $messageFormat)
    {
        $this->body = $body;
        $this->messageFormat = $messageFormat;
    }

    /**
     * Calls the matcher function body and returns true or false depending on
     * the success of the match condition.
     *
     * @param mixed $value Value being tested
     * @param array $args Any additional arguments required by matcher
     * @return bool
     */
    public function match($value, array $args)
    {
        array_unshift($args, $value);

        return call_user_func_array($this->body, $args);
    }

    /**
     * Returns the message format as passed to the constructor
     *
     * @return string
     */
    public function getMessageFormat()
    {
        return $this->messageFormat;
    }
}
