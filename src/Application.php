<?php

namespace Tusk;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @see http://symfony.com/doc/current/components/console/single_command_tool.html
 */
class Application extends BaseApplication
{
    private $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
        parent::__construct();
    }

    public function getDefinition()
    {
        $definition = parent::getDefinition();
        $definition->setArguments();
        return $definition;
    }

    protected function getCommandName(InputInterface $input)
    {
        return $this->command->getName();
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = $this->command;
        return $commands;
    }
}
