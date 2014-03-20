<?php

namespace Tusk;

/**
 * Runs the specs and records the results
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class SpecRunner
{
    private $scoreboard;

    private $specs = [];

    public function __construct(Scoreboard $scoreboard)
    {
        $this->scoreboard = $scoreboard;
    }

    public function add(Spec $spec)
    {
        $this->specs[] = $spec;
    }

    public function run()
    {
        foreach ($this->specs as $spec) {
            if ($spec->isSkipped()) {
                $this->scoreboard->skip();
                continue;
            }

            try {
                $spec->run();
                $this->scoreboard->pass();

            } catch (\Exception $e) {
                $this->scoreboard->fail(
                    $spec->getDescription(),
                    $e->getMessage()
                );
            }
        }
    }
}
