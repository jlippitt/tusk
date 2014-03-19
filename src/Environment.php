<?php

namespace Tusk;

class Environment
{
    private $context;

    private $skipFlag = false;

    public function setContext(AbstractContext $context = null)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function skip(\Closure $body)
    {
        $this->skipFlag = true;
        $body();
        $this->skipFlag = false;
    }

    public function isSkipFlagSet()
    {
        return $this->skipFlag;
    }
}
