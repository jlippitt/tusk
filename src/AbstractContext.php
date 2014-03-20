<?php

namespace Tusk;

/**
 * Base class for 'contexts' (i.e. suites and specs)
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
abstract class AbstractContext
{
    private $description;

    private $parent;

    /**
     * @param string $description
     * @param AbstractContext|null $parent
     */
    public function __construct($description, AbstractContext $parent = null)
    {
        $this->description = $description;
        $this->parent = $parent;
    }

    /**
     * Returns the full description of the context. This will include
     * descriptions of all ancestors in the hierarchy, with each being
     * separated by a space.
     *
     * @return string
     */
    public function getDescription()
    {
        if ($this->parent !== null) {
            $description = $this->parent->getDescription() . ' ';
        } else {
            $description = '';
        }

        return $description . $this->description;
    }

    /**
     * Runs the body of the context
     *
     * @param bool $skip If true, the context is being skipped and may behave
     * differently
     */
    public abstract function execute($skip = false);

    /**
     * Returns the parent context, as passed to the constructor
     *
     * @return AbstractContext|null
     */
    protected function getParent()
    {
        return $this->parent;
    }
}
