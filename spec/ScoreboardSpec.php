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

    describe('getPassCount()', function() {
        it('should return the number of passing assertions', function() {
            expect($this->scoreboard->getPassCount())->toBe(3);
        });
    });

    describe('getFailCount()', function() {
        it('should return the number of failing assertions', function() {
            expect($this->scoreboard->getFailCount())->toBe(2);
        });
    });
});
