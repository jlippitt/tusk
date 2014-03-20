<?php

namespace Tusk;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Main console application class. Sets up a Symfony Console application with
 * only one command, which is always run by default.
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 * @see http://symfony.com/doc/current/components/console/single_command_tool.html
 */
class Application extends BaseApplication
{
    private $command;

    /**
     * @param Command $command The default command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinition()
    {
        $definition = parent::getDefinition();
        $definition->setArguments();
        return $definition;
    }

    /**
     * {@inheritDoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return $this->command->getName();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = $this->command;
        return $commands;
    }
}
