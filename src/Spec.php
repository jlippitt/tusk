<?php

namespace Tusk;

class Spec extends AbstractContext
{
    private $body;

    public function __construct($description, \Closure $body, Environment $env)
    {
        parent::__construct($description, $env);

        $this->body = $body;
    }

    protected function executeBody()
    {
        $this->getParent()->executePreHooks();

        $this->body->__invoke();

        $this->getParent()->executePostHooks();
    }
}
