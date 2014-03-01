<?php

namespace Tusk;

class Environment
{
    private $context;

    private $scoreboard;

    private $expectationFactory;

    public static function getInstance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new self(new Scoreboard(), new ExpectationFactory());
        }

        return $instance;
    }

    public function __construct(
        Scoreboard $scoreboard,
        ExpectationFactory $expectationFactory
    ) {
        $this->scoreboard = $scoreboard;
        $this->expectationFactory = $expectationFactory;
    }

    public function setContext(AbstractContext $context = null)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getScoreboard()
    {
        return $this->scoreboard;
    }

    public function getExpectationFactory()
    {
        return $this->expectationFactory;
    }
}
