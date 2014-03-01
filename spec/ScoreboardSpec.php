<?php

use Tusk\Scoreboard;

describe('Scoreboard', function() {
    beforeEach(function() {
        $this->scoreboard = new Scoreboard();
        $this->scoreboard->pass();
        $this->scoreboard->fail();
        $this->scoreboard->pass();
        $this->scoreboard->fail();
        $this->scoreboard->pass();
    });

    describe('getFailCount()', function() {
        it('should return the number of failed specs', function() {
            expect($this->scoreboard->getFailCount())->toBe(2);
        });
    });

    describe('getSpecCount()', function() {
        it('should the sum of all passed and failed specs', function() {
            expect($this->scoreboard->getSpecCount())->toBe(5);
        });
    });
});
