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
});
