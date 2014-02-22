<?php

namespace Tusk;

class Stack
{
    public static function getInstance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    public function push($text)
    {
        // Nothing yet
    }

    public function pop()
    {
        // Nothing yet
    }
}
