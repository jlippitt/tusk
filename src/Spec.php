<?php

namespace Tusk;

class Spec extends AbstractContext
{
    private $body;

    private $scoreboard;

    public function __construct($description, \Closure $body, Environment $env, Scoreboard $scoreboard)
    {
        parent::__construct($description, $env);

        $this->body = $body;
        $this->scoreboard = $scoreboard;
    }

    protected function executeBody($skip = false)
    {
        if ($skip) {
            $this->scoreboard->skip();

        } else {
            $scope = clone $this->getParent()->getScope();

            try {
                $this->getParent()->executePreHooks($scope);

                $this->body->bindTo($scope, $scope)->__invoke();

                $this->getParent()->executePostHooks($scope);

                $this->scoreboard->pass();

            } catch (\Exception $e) {
                $this->scoreboard->fail($this->getDescription(), $e->getMessage());
            }
        }
    }
}
