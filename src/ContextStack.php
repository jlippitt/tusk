<?php

namespace Tusk;

/**
 * 'Wraps' the running of any specs and suites and keeps track of information
 * relating to application state
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class ContextStack
{
    private $context;

    private $skipFlag = false;

    /**
     * Executes a context (i.e. spec or suite). Keeps track of previous
     * context and will restore it once context has finished running.
     *
     * @param AbstractContext $context
     */
    public function execute(AbstractContext $context)
    {
        $oldContext = $this->context;
        $this->context = $context;
        $context->setUp($this->skipFlag);
        $this->context = $oldContext;
    }

    /**
     * Returns the current context
     * 
     * @return AbstractContext
     */
    public function getContext()
    {
        return $this->context;
    }
}
