<?php

namespace Tusk;

/**
 * Represents a suite of specs, i.e. a 'describe' block
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Suite extends AbstractContext
{
    private $body;

    private $preHooks = array();

    private $postHooks = array();

    private $scope;

    /**
     * @param string $description
     * @param \Closure $body
     * @param AbstractContext|null $parent
     */
    public function __construct(
        $description,
        \Closure $body,
        AbstractContext $parent = null
    ) {
        parent::__construct($description, $parent);
        $this->body = $body;
    }

    /**
     * Adds a pre-hook, to be run before all specs in the suite
     *
     * @param \Closure $body Hook body
     */
    public function addPreHook(\Closure $body)
    {
        $this->preHooks[] = $body;
    }

    /**
     * Adds a post-hook, to be run after all specs in the suite
     *
     * @param \Closure $body Hook body
     */
    public function addPostHook(\Closure $body)
    {
        $this->postHooks[] = $body;
    }

    /**
     * Executes all pre-hooks in this and any parent suites. Hooks in parent
     * suites will be run before any hooks in this suite.
     *
     * @param \stdClass $scope Scope to be bound to '$this' for each hook
     */
    public function executePreHooks(\stdClass $scope)
    {
        if ($this->getParent() !== null) {
            $this->getParent()->executePreHooks($scope);
        }

        foreach ($this->preHooks as $hook) {
            $hook->bindTo($scope, $scope)->__invoke();
        }
    }

    /**
     * Executes all post-hooks in this and any parent suites. Hooks in parent
     * suites will be run after any hooks in this suite.
     *
     * @param \stdClass $scope Scope to be bound to '$this' for each hook
     */
    public function executePostHooks(\stdClass $scope)
    {
        foreach ($this->postHooks as $hook) {
            $hook->bindTo($scope, $scope)->__invoke();
        }

        if ($this->getParent() !== null) {
            $this->getParent()->executePostHooks($scope);
        }
    }

    /**
     * Returns scope which is bound to '$this' for this suite
     *
     * @return \stdClass
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($skip = false)
    {
        if ($this->getParent() !== null) {
            $this->scope = clone $this->getParent()->getScope();
        } else {
            $this->scope = new \stdClass();
        }

        $this->body->bindTo($this->scope, $this->scope)->__invoke();
    }
}
