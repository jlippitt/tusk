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

        // We catch any exception and re-throw it later, as we still need to
        // run tear-down logic even if something breaks in the spec body
        try {
            $this->body->bindTo($scope, $scope)->__invoke();

        } catch (\Exception $bodyError) {}

        try {
            $this->getParent()->executePostHooks($scope);

        } catch (\Exception $postHookError) {
            // If both the spec body and a post-hook threw an exception, we
            // re-throw the exception from the body in preference
            if (!isset($bodyError)) {
                throw $postHookError;
            }
        }

        if (isset($bodyError)) {
            throw $bodyError;
        }
    }
}
