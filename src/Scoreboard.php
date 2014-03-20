<?php

namespace Tusk;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Keeps 'score', tracking how many specs have passed, failed or been skipped
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Scoreboard
{
    private $output;

    private $passed = 0;

    private $failed = [];

    private $skipped = 0;

    /**
     * @param OutputInterface $output Console output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Marks a spec as passed and displays progress on the console
     */
    public function pass()
    {
        ++$this->passed;
        $this->output->write('<info>.</info>');
    }

    /**
     * Marks a spec as failed and displays progress on the console
     *
     * @param string $spec Full spec description
     * @param string $reason Reason spec failed (i.e. failure message)
     */
    public function fail($spec, $reason)
    {
        $this->failed[$spec] = $reason;
        $this->output->write('<error>F</error>');
    }

    /**
     * Marks a spec as skipped and displays progress on the console
     */
    public function skip()
    {
        ++$this->skipped;
        $this->output->write('<comment>S</comment>');
    }

    /**
     * Returns the total number of specs that have been run
     * 
     * @return int
     */
    public function getSpecCount()
    {
        return $this->passed + count($this->failed) + $this->skipped;
    }

    /**
     * Returns the number failed specs
     * 
     * @return int
     */
    public function getFailCount()
    {
        return count($this->failed);
    }

    /**
     * Returns the number skipped specs
     * 
     * @return int
     */
    public function getSkipCount()
    {
        return $this->skipped;
    }

    /**
     * Returns details of the failed specs. This will be an associative array
     * with the spec descriptions as keys and the failure reasons as values.
     *
     * @return string[]
     */
    public function getFailedSpecs()
    {
        return $this->failed;
    }
}
