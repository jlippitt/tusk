<?php

use Mockery as m;
use Tusk\ProgressOutput;

describe('ProgressOutput', function() {
    beforeEach(function() {
        $this->output = m::mock(
            'Symfony\Component\Console\Output\OutputInterface',
            ['write' => null]
        );

        $this->progressOutput = new ProgressOutput($this->output);
    });

    afterEach(function() {
        m::close();
    });

    describe('pass()', function() {
        it('should print a dot to the console', function() {
            $this->output
                ->shouldReceive('write')
                ->with('<info>.</info>')
                ->once()
            ;

            $this->progressOutput->pass();
        });
    });

    describe('fail()', function() {
        it('should print the letter F to the console', function() {
            $this->output
                ->shouldReceive('write')
                ->with('<error>F</error>')
                ->once()
            ;

            $this->progressOutput->fail();
        });
    });

    describe('skip()', function() {
        it('should print the letter S to the console', function() {
            $this->output
                ->shouldReceive('write')
                ->with('<comment>S</comment>')
                ->once()
            ;

            $this->progressOutput->skip();
        });
    });

    describe('done()', function() {
        it('should display statistics after the final line', function() {
            $this->progressOutput->setTotalSpecs(120);

            $this->output
                ->shouldReceive('writeln')
                ->with(" 120/120\n")
                ->once()
            ;

            $this->progressOutput->done();
        });
    });

    it('should break the output at the appropriate point and display progress stats', function() {
        $this->progressOutput->setTotalSpecs(120);

        $dotCount = 0;

        $this->output
            ->shouldReceive('write')
            ->andReturnUsing(function() use (&$dotCount) {
                ++$dotCount;
            })
        ;

        // TODO: We should do two lines, at least
        $this->output
            ->shouldReceive('writeln')
            ->with("  72/120\n")
            ->once()
            ->andReturnUsing(function() use (&$dotCount) {
                expect($dotCount)->toBe(72);
            })
        ;

        for ($i = 0; $i < 100; ++$i) {
            $this->progressOutput->pass();
        }

        expect($dotCount)->toBe(100);
    });
});
