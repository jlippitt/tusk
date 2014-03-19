<?php

namespace Tusk;

abstract class AbstractContext
{
    private $description;

    private $parent;

    public function __construct($description, AbstractContext $parent = null)
    {
        $this->description = $description;
        $this->parent = $parent;
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

    public abstract function execute($skip = false);

    protected function getParent()
    {
        return $this->parent;
    }
}
