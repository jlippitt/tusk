<?php

namespace Tusk;

class Environment
{
    private $context;

    public static function getInstance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    public function setContext(AbstractContext $context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
