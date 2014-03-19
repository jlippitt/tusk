<?php

namespace Tusk;

class Environment
{
    private $context;

    private $skipFlag = false;

    public function execute(AbstractContext $context)
    {
        $oldContext = $this->context;
        $this->context = $context;
        $context->execute($this->skipFlag);
        $this->context = $oldContext;
    }

    public function skip(\Closure $body)
    {
        $this->skipFlag = true;
        $body();
        $this->skipFlag = false;
    }

    public function getContext()
    {
        return $this->context;
    }
}
