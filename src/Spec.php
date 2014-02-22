<?php

namespace Tusk;

class Spec extends AbstractContext
{
    private $body;

    public function __construct($description, \Closure $body, Environment $env)
    {
        parent::__construct($description, $env);

        $this->body = $body;
    }

    protected function executeBody()
    {
        $scope = new \stdClass();

        $this->getParent()->executePreHooks($scope);

        $this->body->bindTo($scope, $scope)->__invoke();

        $this->getParent()->executePostHooks($scope);
    }
}
