<?php

namespace Tusk;

class Spec extends AbstractContext
{
    public function __construct($description, Suite $parent)
    {
        parent::__construct($description, $parent);
    }
}
