<?php

namespace Tusk;

class Context
{
    private $description;

    private $body;

    private $env;

    private $parent;

    public function __construct($description, \Closure $body, Environment $env)
    {
        $this->description = $description;
        $this->body = $body;
        $this->env = $env;
    }

    public function getDescription()
    {
        if ($this->parent !== null) {
            $description = $this->parent->getDescription() . ' ';
        } else {
            $description = '';
        }

        return $description . $this->description;
    }

    public function execute()
    {
        $this->parent = $this->env->getContext();

        $this->env->setContext($this);

        $this->body->__invoke();

        $this->env->setContext($this->parent);
    }
}
