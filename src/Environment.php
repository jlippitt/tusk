<?php

namespace Tusk;

class Environment
{
    private $context;

    private $expectationFactory;

    public static function getInstance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new self(new ExpectationFactory());
        }

        return $instance;
    }

    public function __construct(ExpectationFactory $expectationFactory)
    {
        $this->expectationFactory = $expectationFactory;
    }

    public function setContext(AbstractContext $context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getExpectationFactory()
    {
        return $this->expectationFactory;
    }
}
