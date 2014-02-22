<?php

namespace Tusk;

class Suite extends AbstractContext
{
    private $body;

    private $preHooks = array();

    private $postHooks = array();

    public function __construct($description, \Closure $body, Environment $env)
    {
        parent::__construct($description, $env);

        $this->body = $body;
    }

    public function addPreHook(\Closure $body)
    {
        $this->preHooks[] = $body;
    }

    public function addPostHook(\Closure $body)
    {
        $this->postHooks[] = $body;
    }

    public function executePreHooks(\stdClass $scope)
    {
        if ($this->getParent() !== null) {
            $this->getParent()->executePreHooks($scope);
        }

        foreach ($this->preHooks as $hook) {
            $hook->bindTo($scope, $scope)->__invoke();
        }
    }

    public function executePostHooks(\stdClass $scope)
    {
        foreach ($this->postHooks as $hook) {
            $hook->bindTo($scope, $scope)->__invoke();
        }

        if ($this->getParent() !== null) {
            $this->getParent()->executePostHooks($scope);
        }
    }

    protected function executeBody()
    {
        $this->body->__invoke();
    }
}
