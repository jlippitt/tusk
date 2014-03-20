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
        $context->execute($this->skipFlag);
        $this->context = $oldContext;
    }

    /**
     * Executes a 'skip block', i.e. a section of code during which the 'skip
     * flag' (denoting that specs should be skipped, but still recorded as
     * having run) will be set to true. After the block has finished executing,
     * it will be set back to false.
     *
     * @param \Closure $body
     */
    public function skip(\Closure $body)
    {
        $this->skipFlag = true;
        $body();
        $this->skipFlag = false;
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
