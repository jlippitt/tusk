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
    const LINE_LENGTH = 80;

    private $totalSpecs;

    private $specsRun = 0;

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
        $this->lineBreak();
    }

    /**
     * Marks a spec as failed and displays progress on the console
     */
    public function fail()
    {
        $this->output->write('<error>F</error>');
        $this->lineBreak();
    }

    /**
     * Marks a spec as skipped and displays progress on the console
     */
    public function skip()
    {
        $this->output->write('<comment>S</comment>');
        $this->lineBreak();
    }

    /**
     * Called when all tests have completed. Displays final stats.
     */
    public function done()
    {
        $this->output->writeln(" {$this->totalSpecs}/{$this->totalSpecs}\n");
    }

    private function lineBreak()
    {
        ++$this->specsRun;

        // Display the stats followed by a line break, if we have reached the appropriate
        // number of specs
        $maxSpecsOnLine = self::LINE_LENGTH -
            strlen(" {$this->totalSpecs}/{$this->totalSpecs}");

        if (($this->specsRun % $maxSpecsOnLine) === 0) {
            $paddingSize = strlen((string)$this->totalSpecs) -
                strlen((string)$this->specsRun);

            $padding = '';

            for ($i = 0; $i < $paddingSize; ++$i) {
                $padding .= ' ';
            }

            $this->output->writeln(" {$padding}{$this->specsRun}/{$this->totalSpecs}\n");
        }
    }
}
