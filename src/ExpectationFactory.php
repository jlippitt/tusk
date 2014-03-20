<?php

namespace Tusk;

/**
 * Factory class for expectations. Used to construct expectations with a set of
 * matchers and any other required dependencies.
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class ExpectationFactory
{
    private $prettyPrinter;

    private $matchers = array();

    /**
     * @param PrettyPrinter $prettyPrinter
     */
    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->prettyPrinter = $prettyPrinter;
    }

    /**
     * Adds a matcher that will be passed on to all expectations constructed
     * by this instance
     *
     * @param string $name Matcher name, e.g. 'toBe'
     * @param Matcher $matcher Matcher instance
     */
    public function addMatcher($name, Matcher $matcher)
    {
        $this->matchers[$name] = $matcher;
    }

    /**
     * Constructs an Expectation object, passing on all the matchers added to
     * this instance
     *
     * @return Expectation
     */
    public function createExpectation($value)
    {
        return new Expectation(
            $value,
            $this->matchers,
            $this->prettyPrinter
        );
    }
}
