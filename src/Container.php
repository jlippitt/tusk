<?php

namespace Tusk;

use Pimple;
use Symfony\Component\Console\Output\ConsoleOutput;

class Container extends Pimple
{
    public static function getInstance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    public function __construct()
    {
        parent::__construct();

        $this['Application'] = function($c) {
            return new Application($c['Command']);
        };

        $this['Command'] = function($c) {
            return new Command($c['Scoreboard']);
        };

        $this['ConsoleOutput'] = function() {
            return new ConsoleOutput();
        };

        $this['Environment'] = function() {
            return new Environment();
        };

        $this['Scoreboard'] = function($c) {
            return new Scoreboard($c['ConsoleOutput']);
        };

        $this['PrettyPrinter'] = function() {
            return new PrettyPrinter();
        };

        $this['ExpectationFactory'] = function($c) {
            $expectationFactory = new ExpectationFactory($c['PrettyPrinter']);

            /**
             * Equality
             */

            $expectationFactory->addMatcher(
                'toBe',
                new Matcher(
                    function ($value, $expected) {
                        return $value === $expected;
                    },
                    'to be {0}'
                )
            );

            $expectationFactory->addMatcher(
                'toEqual',
                new Matcher(
                    function ($value, $expected) {
                        return $value == $expected;
                    },
                    ' to equal {0}'
                )
            );

            $expectationFactory->addMatcher(
                'toBeGreaterThan',
                new Matcher(
                    function ($value, $expected) {
                        return $value > $expected;
                    },
                    'to be greater than {0}'
                )
            );

            $expectationFactory->addMatcher(
                'toBeGreaterThanOrEqualTo',
                new Matcher(
                    function ($value, $expected) {
                        return $value >= $expected;
                    },
                    'to be greater than or equal to {0}'
                )
            );

            $expectationFactory->addMatcher(
                'toBeLessThan',
                new Matcher(
                    function ($value, $expected) {
                        return $value < $expected;
                    },
                    'to be less than {0}'
                )
            );

            $expectationFactory->addMatcher(
                'toBeLessThanOrEqualTo',
                new Matcher(
                    function ($value, $expected) {
                        return $value <= $expected;
                    },
                    'to be less than or equal to {0}'
                )
            );

            /**
             * Truthiness
             */

            $expectationFactory->addMatcher(
                'toBeTruthy',
                new Matcher(
                    function ($value) {
                        return (bool)$value;
                    },
                    'to be truthy'
                )
            );

            $expectationFactory->addMatcher(
                'toBeFalsy',
                new Matcher(
                    function ($value) {
                        return !$value;
                    },
                    'to be falsy'
                )
            );

            /**
             * Arrays
             */

            $expectationFactory->addMatcher(
                'toContain',
                new Matcher(
                    function ($value, $expected) {
                        return in_array($expected, $value);
                    },
                    'to contain {0}'
                )
            );

            /**
             * Strings
             */
            $expectationFactory->addMatcher(
                'toMatch',
                new Matcher(
                    function ($value, $pattern) {
                        return preg_match($pattern, $value) > 0;
                    },
                    'to match {0}'
                )
            );

            /**
             * Types
             */

            $expectationFactory->addMatcher(
                'toBeType',
                new Matcher(
                    function ($value, $type) {
                        return gettype($value) === $type;
                    },
                    'to be type {0}'
                )
            );

            $expectationFactory->addMatcher(
                'toBeInstanceOf',
                new Matcher(
                    function ($value, $class) {
                        return $value instanceof $class;
                    },
                    "to be an instance of {0}"
                )
            );

            /**
             * Exceptions
             */

            $expectationFactory->addMatcher(
                'toThrow',
                new Matcher(
                    function ($value, $className = null, $message = null) {
                        try {
                            $value();

                        } catch (\Exception $e) {
                            if (($className === null || $e instanceof $className)
                                && ($message === null || $e->getMessage() === $message)
                            ) {
                                return true;
                            }
                        }

                        return false;
                    },
                    'to throw {0} {1}'
                )
            );

            return $expectationFactory;
        };

        $this['Suite'] = function ($c) {
            return function($description, $body, $parent = null) use ($c) {
                return new Suite($description, $body, $parent);
            };
        };

        $this['Spec'] = function($c) {
            return function($description, $body, $parent) use ($c) {
                return new Spec(
                    $description,
                    $body,
                    $parent,
                    $c['Scoreboard']
                );
            };
        };
    }
}
