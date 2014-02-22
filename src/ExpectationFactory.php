<?php

namespace Tusk;

class ExpectationFactory
{
    private $comparators = array();

    public function __construct()
    {
        /**
         * Equality
         */

        $this->addComparator(
            'toBe',
            new Comparator(
                function ($value, $expected) {
                    return $value === $expected;
                },
                'to be {0}'
            )
        );

        $this->addComparator(
            'toEqual',
            new Comparator(
                function ($value, $expected) {
                    return $value == $expected;
                },
                ' to equal {0}'
            )
        );

        $this->addComparator(
            'toBeGreaterThan',
            new Comparator(
                function ($value, $expected) {
                    return $value > $expected;
                },
                'to be greater than {0}'
            )
        );

        $this->addComparator(
            'toBeGreaterThanOrEqualTo',
            new Comparator(
                function ($value, $expected) {
                    return $value >= $expected;
                },
                'to be greater than or equal to {0}'
            )
        );

        $this->addComparator(
            'toBeLessThan',
            new Comparator(
                function ($value, $expected) {
                    return $value < $expected;
                },
                'to be less than {0}'
            )
        );

        $this->addComparator(
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

        $this->addComparator(
            'toBeTruthy',
            new Comparator(
                function ($value) {
                    return (bool)$value;
                },
                'to be truthy'
            )
        );

        $this->addComparator(
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

        $this->addComparator(
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

        $this->addComparator(
            'toBeType',
            new Comparator(
                function ($value, $type) {
                    return gettype($value) === $type;
                },
                'to be type {0}'
            )
        );

        $this->addComparator(
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

        $this->addComparator(
            'toThrow',
            new Comparator(
                function ($value, $exceptionClass) {
                    try {
                        $value();

                    } catch (\Exception $e) {
                        if ($e instanceof $exceptionClass) {
                            return true;
                        }
                    }

                    return false;
                },
                'to throw {0}'
            )
        );
    }

    public function addComparator($name, Comparator $comparator)
    {
        $this->comparators[$name] = $comparator;
    }

    public function createExpectation($value, AbstractContext $context)
    {
        return new Expectation($value, $this->comparators, $context);
    }
}
