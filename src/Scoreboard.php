<?php

namespace Tusk;

use Symfony\Component\Console\Output\OutputInterface;

class Scoreboard
{
    private $output;

    private $passed = 0;

    private $failed = [];

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function pass()
    {
        ++$this->passed;
        $this->output->write('<info>.</info>');
    }

    public function fail($spec, $reason)
    {
        $this->failed[$spec] = $reason;
        $this->output->write('<error>F</error>');
    }

    public function getFailCount()
    {
        return count($this->failed);
    }

    public function getSpecCount()
    {
        return $this->passed + count($this->failed);
    }

    public function getFailedSpecs()
    {
        return $this->failed;
    }
}
