<?php

namespace Tusk;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Displays test progress in the console
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class ProgressOutput
{
    private $totalSpecs;

    /**
     * @param OutputInterface $output Console output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Sets the total number of specs. This should be done before anything else
     * when running specs.
     *
     * @param int $totalSpecs
     */
    public function setTotalSpecs($totalSpecs)
    {
        $this->totalSpecs = $totalSpecs;
    }

    /**
     * Marks a spec as passed and displays progress on the console
     */
    public function pass()
    {
        $this->output->write('<info>.</info>');
    }

    /**
     * Marks a spec as failed and displays progress on the console
     */
    public function fail()
    {
        $this->output->write('<error>F</error>');
    }

    /**
     * Marks a spec as skipped and displays progress on the console
     */
    public function skip()
    {
        $this->output->write('<comment>S</comment>');
    }
}
