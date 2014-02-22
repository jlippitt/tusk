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

    public function executePreHooks()
    {
        if ($this->getParent() !== null) {
            $this->getParent()->executePreHooks();
        }

        foreach ($this->preHooks as $hook) {
            $hook();
        }
    }

    public function executePostHooks()
    {
        foreach ($this->postHooks as $hook) {
            $hook();
        }

        if ($this->getParent() !== null) {
            $this->getParent()->executePostHooks();
        }
    }

    protected function executeBody()
    {
        $this->body->__invoke();
    }
}
