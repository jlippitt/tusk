tusk
====

A PHP testing framework for elephants

The Basics
----------

**tusk** is a testing framework for PHP in the style of BDD (behaviour-driven development) frameworks such as Jasmine (JavaScript) or RSpec (Ruby).

Each **spec** is denoted by an `it` block in the test file. `describe` blocks are used to group multiple specs into a **suite**. `describe` blocks can be nested to as deep a level as required.

Inside each spec, `expect` is used to create *expectations* which must be satisfied in order for the test to pass.

The canonical example:

    describe('Bowling', function() {
        describe('getScore()', function() {
            it('returns 0 for an all gutter game', function() {
                $bowling = new Bowling();
                
                for ($i = 0; $i < 10; ++$i) {
                    $bowling->hit(0);
                }
                
                expect($bowling->getScore())->toBe(0);
            });
        });
    });
    
Set Up and Tear Down
--------------------

Often you will have code that you want to run before each spec or after each spec within a `describe` block. This can be achieved using `beforeEach` and `afterEach` blocks:

    describe('My Test Suite', function() {
        beforeEach(function() {
            echo "This will run before each spec\n";
        });
        
        afterEach(function() {
            echo "This will run after each spec\n"
        });
        
        // ...
    });
    
Hooks defined on an outer `describe` block will also run before/after each spec inside any nested `describe` blocks.

Variable Scoping
----------------

PHP's closures (anonymous functions) have an odd quirk, in that if you want to capture any variables from the outer scope within the closure, you have to explicitly tell PHP with the `use` keyword.

Given our extensive use of anonymous functions for defining suites and specs, this could get quite tedious:

    describe('Bowling', function() {
        $bowling = null;
        
        beforeEach(function() use (&$bowling) {
            $bowling = new Bowling();
        });
        
        describe('getScore()', function() use (&$bowling) {
            it('returns 0 for an all gutter game', function() use (&$bowling) {
                for ($i = 0; $i < 10; ++$i) {
                    $bowling->hit(0);
                }
                
                expect($bowling->getScore())->toBe(0);
            });
        });
    });
    
However, as of PHP 5.4, it's possible to bind the '$this' variable inside a closure to an arbitrary value. tusk makes extensive use of this and things work as you would expect:

    describe('Bowling', function() {
        beforeEach(function() {
            $this->bowling = new Bowling();
        });
        
        describe('getScore()', function() {
            it('returns 0 for an all gutter game', function() {
                for ($i = 0; $i < 10; ++$i) {
                    $this->bowling->hit(0);
                }
                
                expect($this->bowling->getScore())->toBe(0);
            });
        });
    });
    
Expectations and Matchers
-------------------------

Expectations provide a number of built-in *matchers*:

* **toBe(expectedValue)** Compares values using the `===` operator

* **toEqual(expectedValue)** Compares values using the `==` operator

* **toBeGreaterThan(expectedValue)** Compares values using the `>` operator

* **toBeGreaterThanOrEqualTo(expectedValue)** Compares values using the `>=` operator

* **toBeLessThan(expectedValue)** Compares values using the `<` operator

* **toBeLessThanOrEqualTo(expectedValue)** Compares values using the `<=` operator

* **toBeTruthy()** Tests that the value is 'truthy' according to [boolean conversion rules](http://www.php.net/manual/en/language.types.boolean.php)

* **toBeFalsy()** Tests that the value is 'falsy' according to [boolean conversion rules](http://www.php.net/manual/en/language.types.boolean.php)

* **toBeFalsy()** Tests that the value is 'falsy' according to [boolean conversion rules](http://www.php.net/manual/en/language.types.boolean.php)

* **toContain(expectedValue)** Tests that an array contains a given value. This uses the internal [in\_array](http://www.php.net/manual/en/function.in-array.php) function.

* **toMatch(regex)** Tests that a string value matches the given regular expression. See [preg\_match](http://www.php.net/manual/en/function.preg-match.php).

* **toBeType(type)** Tests that the value matches a given internal type. See [gettype](http://www.php.net/manual/en/function.gettype.php).

* **toBeInstanceOf(className)** Tests that the value is an instance of the given class or interface

* **toThrow([className[, message])** Tests that a Closure (anonymous function) throws an exception when run. The exception class and error message can also be supplied, in which case these must be matched as well in order for the expectation to succeed.

All expectations can be negated by changing `to` to `notTo`, e.g. `toBe` becomes `notToBe` and `toEqual` becomes `notToEqual`.

Defining Custom Matchers
------------------------

Custom matchers can be defined by adding them to the internal `ExpectationFactory` class, which will then make them available on all expectations within your specs. To do this, you'll need to first retrieve the `ExpectationFactory` from the `Pimple` dependency injection container, and then call the `addMatch` method for each matcher you want to add:

    <?php

    use Tusk\Container;
    use Tusk\Matcher;

    $expectationFactory = Container::getInstance()['ExpectationFactory'];

    $expectationFactory->addMatcher(
        'toBeDivisibleBy',
        new Matcher(
            function($value, $divisor) {
                return $value % $divisor === 0;
            },
            'to be divisible by {0}'
        )
    );

The matcher function can take any number of parameters, though the first parameter will always be the value that was passed to the `expect` function when creating the expectation. The remaining arguments are provided on the matcher method itself.

The second parameter to the matcher object determines the message that will be displayed to the user when the matcher does not succeed. Any of the arguments passed to the matcher method can be inserted into this string using {0}, {1}, {2}, etc. (where the number is their zero-indexed position in the argument list).

License
-------

tusk is released under the MIT License. See the [LICENSE](https://github.com/jlippitt/tusk/blob/master/LICENSE) file for more details.
