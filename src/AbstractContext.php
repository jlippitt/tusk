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

    private $skip;

    /**
     * @param string $description
     * @param AbstractContext|null $parent
     * @param bool $skip
     */
    public function __construct($description, AbstractContext $parent = null, $skip = false)
    {
        $this->description = $description;
        $this->parent = $parent;
        $this->skip = $skip;
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
     * Returns true if the suite/spec should be skipped
     *
     * @return bool
     */
    public function isSkipped()
    {
        return ($this->parent !== null && $this->parent->isSkipped())
            || $this->skip;
    }

    /**
     * Performs initial set up work for the context
     *
     * @throw \Exception If there is an error setting up the context
     */
    public abstract function setUp();

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
