<?php

namespace Tusk;

/**
 * Represents an individual spec, i.e. an 'it' block
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Spec extends AbstractContext
{
    private $body;

    private $scoreboard;

    private $specRunner;

    /**
     * @param string $description
     * @param \Closure $body
     * @param AbstractContext $parent
     * @param bool $skip
     * @param SpecRunner $specRunner
     */
    public function __construct(
        $description,
        \Closure $body,
        AbstractContext $parent,
        SpecRunner $specRunner,
        $skip = false
    ) {
        parent::__construct($description, $parent, $skip);
        $this->body = $body;
        $this->specRunner = $specRunner;
    }

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->specRunner->add($this);
    }

    /**
     * Runs the spec
     *
     * @throw \Exception If the spec fails or there is an error
     */
    public function run()
    {
        $scope = clone $this->getParent()->getScope();

        $this->getParent()->executePreHooks($scope);

        $this->body->bindTo($scope, $scope)->__invoke();

        $this->getParent()->executePostHooks($scope);
    }
}
