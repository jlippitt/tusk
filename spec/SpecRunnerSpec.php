<?php

use Mockery as m;
use Tusk\SpecRunner;

describe('SpecRunner', function() {
    beforeEach(function() {
        $this->progressOutput = m::mock('Tusk\ProgressOutput');
        $this->specRunner = new SpecRunner($this->progressOutput);
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

            $this->progressOutput->shouldReceive('setTotalSpecs')->with(3)->once()->ordered();
            $this->progressOutput->shouldReceive('pass')->times(3)->ordered();
            $this->progressOutput->shouldReceive('done')->once()->ordered();

            $this->specRunner->run();

            expect($this->specRunner->getFailCount())->toBe(0);
            expect($this->specRunner->getFailedSpecs())->toBe([]);
            expect($this->specRunner->getSkipCount())->toBe(0);
        });

        it('should catch exceptions and fail the spec when they occur', function() {
            $spec = m::mock('Tusk\Spec', [
                'getDescription' => 'failing spec',
                'isSkipped' => false
            ]);

            $exception = new \Exception('error');

            $spec
                ->shouldReceive('run')
                ->once()
                ->andThrow($exception)
            ;

            $this->specRunner->add($spec);

            $spec = m::mock('Tusk\Spec', [
                'getDescription' => 'passing spec',
                'isSkipped' => false
            ]);

            $spec = m::mock('Tusk\Spec', ['isSkipped' => false]);
            $spec->shouldReceive('run')->once();

            $this->specRunner->add($spec);

            $this->progressOutput->shouldReceive('setTotalSpecs')->with(2)->once()->ordered();
            $this->progressOutput->shouldReceive('fail')->once()->ordered();
            $this->progressOutput->shouldReceive('pass')->once()->ordered();
            $this->progressOutput->shouldReceive('done')->once()->ordered();

            $this->specRunner->run();

            expect($this->specRunner->getFailCount())->toBe(1);
            expect($this->specRunner->getFailedSpecs())->toBe(['failing spec' => $exception]);
            expect($this->specRunner->getSkipCount())->toBe(0);
        });

        it('should not run any specs where "isSkipped" returns true', function() {
            $spec = m::mock('Tusk\Spec', ['isSkipped' => true]);
            $spec->shouldReceive('run')->never();

            $this->specRunner->add($spec);

            $spec = m::mock('Tusk\Spec', ['isSkipped' => false]);
            $spec->shouldReceive('run')->once();

            $this->specRunner->add($spec);

            $this->progressOutput->shouldReceive('setTotalSpecs')->with(2)->once()->ordered();
            $this->progressOutput->shouldReceive('skip')->once()->ordered();
            $this->progressOutput->shouldReceive('pass')->once()->ordered();
            $this->progressOutput->shouldReceive('done')->once()->ordered();

            $this->specRunner->run();

            expect($this->specRunner->getFailCount())->toBe(0);
            expect($this->specRunner->getFailedSpecs())->toBe([]);
            expect($this->specRunner->getSkipCount())->toBe(1);
        });
    });
});
