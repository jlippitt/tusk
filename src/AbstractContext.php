<?php

namespace Tusk;

class AbstractContext implements ContextInterface
{
    private $description;

    private $parent;

    public function __construct($description, ContextInterface $parent = null)
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

    public function getParent()
    {
        return $this->parent;
    }
}
