<?php

use Mockery as m;
use Tusk\Scoreboard;

describe('Scoreboard', function() {
    beforeEach(function() {
        $this->output = m::mock(
            'Symfony\Component\Console\Output\OutputInterface',
            ['write' => null]
        );

        $this->scoreboard = new Scoreboard($this->output);
    });

    afterEach(function() {
        m::close();
    });

    describe('pass()', function() {
        it('should increment the spec count', function() {
            expect($this->scoreboard->getSpecCount())->toBe(0);
            $this->scoreboard->pass();
            expect($this->scoreboard->getSpecCount())->toBe(1);
            $this->scoreboard->pass();
            expect($this->scoreboard->getSpecCount())->toBe(2);
        });

        it('should print a dot to the console', function() {
            $this->output
                ->shouldReceive('write')
                ->with('<info>.</info>')
                ->once()
            ;

            $this->scoreboard->pass();
        });
    });

    describe('fail()', function() {
        it('should increment the spec count', function() {
            expect($this->scoreboard->getSpecCount())->toBe(0);
            $this->scoreboard->fail('spec 1', 'it broke');
            expect($this->scoreboard->getSpecCount())->toBe(1);
            $this->scoreboard->fail('spec 2', 'it broke even more');
            expect($this->scoreboard->getSpecCount())->toBe(2);
        });

        it('should increment the fail count', function() {
            expect($this->scoreboard->getFailCount())->toBe(0);
            $this->scoreboard->fail('spec 1', 'it broke');
            expect($this->scoreboard->getFailCount())->toBe(1);
            $this->scoreboard->fail('spec 2', 'it broke even more');
            expect($this->scoreboard->getFailCount())->toBe(2);
        });

        it('should print the letter F to the console', function() {
            $this->output
                ->shouldReceive('write')
                ->with('<error>F</error>')
                ->once()
            ;

            $this->scoreboard->fail('spec 1', 'it broke');
        });
    });

    describe('skip()', function() {
        it('should increment the spec count', function() {
            expect($this->scoreboard->getSpecCount())->toBe(0);
            $this->scoreboard->skip();
            expect($this->scoreboard->getSpecCount())->toBe(1);
            $this->scoreboard->skip();
            expect($this->scoreboard->getSpecCount())->toBe(2);
        });

        it('should increment the skip count', function() {
            expect($this->scoreboard->getSkipCount())->toBe(0);
            $this->scoreboard->skip();
            expect($this->scoreboard->getSkipCount())->toBe(1);
            $this->scoreboard->skip();
            expect($this->scoreboard->getSkipCount())->toBe(2);
        });

        it('should print the letter S to the console', function() {
            $this->output
                ->shouldReceive('write')
                ->with('<comment>S</comment>')
                ->once()
            ;

            $this->scoreboard->skip();
        });
    });

    describe('getFailedSpecs()', function() {
        it('should return an associative array mapping spec descriptions to failure messages', function() {
            $this->scoreboard->fail('spec 1', 'it broke');
            $this->scoreboard->fail('spec 2', 'it broke even more');

            expect($this->scoreboard->getFailedSpecs())->toBe([
                'spec 1' => 'it broke',
                'spec 2' => 'it broke even more'
            ]);
        });
    });
});
