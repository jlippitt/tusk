<?php

class Bowling
{
    private $score = 0;

    public function hit($pins)
    {
        // Nothing yet
    }

    public function getScore()
    {
        return $this->score;
    }
}

describe('Bowling', function() {
    describe('getScore()', function() {
        $this->totalPins = 10;

        beforeEach(function() {
            $this->bowling = new Bowling();
        });

        it('returns 0 for all gutter game', function() {
            for ($i = 0; $i < 20; ++$i) {
                $this->bowling->hit(0);
            }

            expect($this->bowling->getScore())->toBe(0);
            expect($this->bowling->getScore())->notToBe(10);
        });

        it('returns 10 for a strike', function() {
            $this->bowling->hit($this->totalPins);

            expect($this->bowling->getScore())->toBe($this->totalPins);
        });
    });
});
