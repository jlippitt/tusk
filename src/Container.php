<?php

namespace Tusk;

use Pimple;

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

        $this['Environment'] = function() {
            return new Environment();
        };

        $this['Scoreboard'] = function() {
            return new Scoreboard();
        };

        $this['ExpectationFactory'] = function($c) {
            $expectationFactory = new ExpectationFactory($c['Environment']);

            /**
             * Equality
             */

            $expectationFactory->addComparator(
                'toBe',
                new Comparator(
                    function ($value, $expected) {
                        return $value === $expected;
                    },
                    'to be {0}'
                )
            );

            $expectationFactory->addComparator(
                'toEqual',
                new Comparator(
                    function ($value, $expected) {
                        return $value == $expected;
                    },
                    ' to equal {0}'
                )
            );

            $expectationFactory->addComparator(
                'toBeGreaterThan',
                new Comparator(
                    function ($value, $expected) {
                        return $value > $expected;
                    },
                    'to be greater than {0}'
                )
            );

            $expectationFactory->addComparator(
                'toBeGreaterThanOrEqualTo',
                new Comparator(
                    function ($value, $expected) {
                        return $value >= $expected;
                    },
                    'to be greater than or equal to {0}'
                )
            );

            $expectationFactory->addComparator(
                'toBeLessThan',
                new Comparator(
                    function ($value, $expected) {
                        return $value < $expected;
                    },
                    'to be less than {0}'
                )
            );

            $expectationFactory->addComparator(
                'toBeLessThanOrEqualTo',
                new Comparator(
                    function ($value, $expected) {
                        return $value <= $expected;
                    },
                    'to be less than or equal to {0}'
                )
            );

            /**
             * Truthiness
             */

            $expectationFactory->addComparator(
                'toBeTruthy',
                new Comparator(
                    function ($value) {
                        return (bool)$value;
                    },
                    'to be truthy'
                )
            );

            $expectationFactory->addComparator(
                'toBeFalsy',
                new Comparator(
                    function ($value) {
                        return !$value;
                    },
                    'to be falsy'
                )
            );

            /**
             * Arrays
             */

            $expectationFactory->addComparator(
                'toContain',
                new Comparator(
                    function ($value, $expected) {
                        return in_array($expected, $value);
                    },
                    'to contain {0}'
                )
            );

            /**
             * Types
             */

            $expectationFactory->addComparator(
                'toBeType',
                new Comparator(
                    function ($value, $type) {
                        return gettype($value) === $type;
                    },
                    'to be type {0}'
                )
            );

            $expectationFactory->addComparator(
                'toBeInstanceOf',
                new Comparator(
                    function ($value, $class) {
                        return $value instanceof $class;
                    },
                    "to be an instance of {0}"
                )
            );

            /**
             * Exceptions
             */

            $expectationFactory->addComparator(
                'toThrow',
                new Comparator(
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
            return function($description, $body) use ($c) {
                return new Suite($description, $body, $c['Environment']);
            };
        };

        $this['Spec'] = function($c) {
            return function($description, $body) use ($c) {
                return new Spec(
                    $description,
                    $body,
                    $c['Environment'],
                    $c['Scoreboard']
                );
            };
        };
    }
}
