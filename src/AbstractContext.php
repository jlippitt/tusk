<?php

namespace Tusk;

abstract class AbstractContext
{
    private $description;

    private $env;

    private $parent;

    public function __construct($description, Environment $env)
    {
        $this->description = $description;
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

        $this->executeBody($this->env->isSkipFlagSet());

        $this->env->setContext($this->parent);
    }

    protected abstract function executeBody($skip = false);

    protected function getParent()
    {
        return $this->parent;
    }
}
