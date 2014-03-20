<?php

namespace Tusk;

/**
 * Represents an individual spec, i.e. an 'it' block
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Spec extends AbstractContext
{
    private $body;

    private $scoreboard;

    /**
     * @param string $description
     * @param \Closure $body
     * @param AbstractContext $parent
     * @param Scoreboard $scoreboard
     */
    public function __construct(
        $description,
        \Closure $body,
        AbstractContext $parent,
        Scoreboard $scoreboard
    ) {
        parent::__construct($description, $parent);
        $this->body = $body;
        $this->scoreboard = $scoreboard;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($skip = false)
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
