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
    describe('getScore', function() {
        it('returns 0 for all gutter game', function() {
            $bowling = new Bowling();

            for ($i = 0; $i < 20; ++$i) {
                $bowling->hit(0);
            }

            expect($bowling->getScore())->toBe(0);
        });
    });
});
