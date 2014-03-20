<?php

namespace Tusk;

/**
 * Runs the specs and records the results
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class SpecRunner
{
    private $progressOutput;

    private $specs = [];

    private $failed = [];

    private $skipped = 0;

    public function __construct(ProgressOutput $progressOutput)
    {
        $this->progressOutput = $progressOutput;
    }

    public function add(Spec $spec)
    {
        $this->specs[] = $spec;
    }

    public function run()
    {
        $this->progressOutput->setTotalSpecs($this->getSpecCount());

        foreach ($this->specs as $spec) {
            if ($spec->isSkipped()) {
                ++$this->skipped;
                $this->progressOutput->skip();
                continue;
            }

            try {
                $spec->run();
                $this->progressOutput->pass();

            } catch (\Exception $e) {
                $this->failed[$spec->getDescription()] = $e->getMessage();
                $this->progressOutput->fail();
            }
        }

        $this->progressOutput->done();
    }

    /**
     * Returns the total number of specs
     * 
     * @return int
     */
    public function getSpecCount()
    {
        return count($this->specs);
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
