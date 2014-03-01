<?php

namespace Tusk;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{
    public function __construct(Scoreboard $scoreboard)
    {
        parent::__construct();
        $this->scoreboard = $scoreboard;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run specs')
            ->addArgument(
                'files',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Path(s) to spec file(s)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($input->getArgument('files') as $file) {
            require($file);
        }

        $failCount = $this->scoreboard->getFailCount();

        $result = "{$this->scoreboard->getSpecCount()} specs, {$failCount} failures";

        if ($failCount === 0) {
            $result = "<info>{$result}</info>";
        } else {
            $result = "<error>{$result}</error>";
        }

        $output->writeln($result);
    }
}
