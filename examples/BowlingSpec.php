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
        $bowling = null;

        beforeEach(function() use (&$bowling) {
            $bowling = new Bowling();
        });

        it('returns 0 for all gutter game', function() use (&$bowling) {
            for ($i = 0; $i < 20; ++$i) {
                $bowling->hit(0);
            }

            expect($bowling->getScore())->toBe(0);
        });

        it('returns 10 for a strike', function() use (&$bowling) {
            $bowling->hit(10);

            expect($bowling->getScore())->toBe(10);
        });
    });
});
