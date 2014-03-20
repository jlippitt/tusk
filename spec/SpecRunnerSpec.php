<?php

use Mockery as m;
use Tusk\SpecRunner;

describe('SpecRunner', function() {
    beforeEach(function() {
        $this->scoreboard = m::mock('Tusk\Scoreboard');
        $this->specRunner = new SpecRunner($this->scoreboard);
    });

    afterEach(function() {
        m::close();
    });

    describe('run()', function() {
        it('should run the previously added specs', function() {
            for ($i = 0; $i < 3; ++$i) {
                $spec = m::mock('Tusk\Spec', ['isSkipped' => false]);
                $spec->shouldReceive('run')->once();
                $this->specRunner->add($spec);
            }

            $this->scoreboard->shouldReceive('pass')->times(3);

            $this->specRunner->run();
        });

        it('should not run any specs where "isSkipped" returns true', function() {
            for ($i = 0; $i < 3; ++$i) {
                if ($i === 1) {
                    $spec = m::mock('Tusk\Spec', ['isSkipped' => true]);
                    $spec->shouldReceive('run')->never();

                } else {
                    $spec = m::mock('Tusk\Spec', ['isSkipped' => false]);
                    $spec->shouldReceive('run')->once();
                }

                $this->specRunner->add($spec);
            }

            $this->scoreboard->shouldReceive('pass')->twice();
            $this->scoreboard->shouldReceive('skip')->once();

            $this->specRunner->run();
        });

        it('should catch exceptions and fail the spec when they occur', function() {
            for ($i = 0; $i < 3; ++$i) {
                if ($i === 1) {
                    $spec = m::mock('Tusk\Spec', [
                        'getDescription' => 'failing spec',
                        'isSkipped' => false
                    ]);

                    $spec
                        ->shouldReceive('run')
                        ->once()
                        ->andThrow(new \Exception('error'))
                    ;

                } else {
                    $spec = m::mock('Tusk\Spec', [
                        'getDescription' => 'passing spec',
                        'isSkipped' => false
                    ]);

                    $spec = m::mock('Tusk\Spec', ['isSkipped' => false]);
                    $spec->shouldReceive('run')->once();
                }

                $this->specRunner->add($spec);
            }

            $this->scoreboard->shouldReceive('pass')->twice();

            $this->scoreboard
                ->shouldReceive('fail')
                ->with('failing spec', 'error')
                ->once()
            ;

            $this->specRunner->run();
        });
    });
});
