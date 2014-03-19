<?php

namespace Tusk;

use Symfony\Component\Console\Output\OutputInterface;

class Scoreboard
{
    private $output;

    private $passCount = 0;

    private $failCount = 0;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function pass()
    {
        $this->output->write('<info>.</info>');
        ++$this->passCount;
    }

    public function fail()
    {
        $this->output->write('<error>F</error>');
        ++$this->failCount;
    }

    public function getFailCount()
    {
        return $this->failCount;
    }

    public function getSpecCount()
    {
        return $this->passCount + $this->failCount;
    }
}
