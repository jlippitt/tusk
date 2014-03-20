<?php

namespace Tusk;

/**
 * Class for an expectation, constructed by the 'expect' global function. This
 * class no defined methods, but instead dynamically routes method calls to the
 * appropriate Matcher instance. For example, 'toBe' and 'notToBe' will both
 * invoke the 'toBe' matcher.
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Expectation
{
    private $value;

    private $matchers;

    private $prettyPrinter;

    /**
     * @param mixed $value
     * @param Matcher[] $matchers
     * @param PrettyPrinter $prettyPrinter
     */
    public function __construct(
        $value,
        array $matchers,
        PrettyPrinter $prettyPrinter
    ) {
        $this->value = $value;
        $this->matchers = $matchers;
        $this->prettyPrinter = $prettyPrinter;
    }

    /**
     * Invokes a matcher based on method name and throws an exception if the
     * matcher returns false. If the matcher name is preceded by 'not', the
     * exception will instead be thrown if the matcher returns true.
     *
     * @param string $method
     * @param mixed[] $args
     * @throws ExpectationException See above
     * @throws \BadMethodCallException If no matcher matches the method name
     */
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

            if ($matcher->match($this->value, $args) === $inverted) {
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
