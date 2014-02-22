<?php

namespace Tusk;

class Suite extends AbstractContext
{
    public function __construct($description, self $parent = null)
    {
        parent::__construct($description, $parent);
    }
}
