<?php

namespace Tusk;

class Environment
{
    private $context;

    public function setContext(AbstractContext $context = null)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
