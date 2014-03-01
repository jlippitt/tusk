<?php

namespace Tusk;

class Scoreboard
{
    private $passCount = 0;

    private $failCount = 0;

    public function pass()
    {
        ++$this->passCount;
    }

    public function fail()
    {
        ++$this->failCount;
    }

    public function getPassCount()
    {
        return $this->passCount;
    }

    public function getFailCount()
    {
        return $this->failCount;
    }
}
