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

        $output->writeln('');

        foreach ($this->scoreboard->getFailedSpecs() as $spec => $reason) {
            $output->writeln("\n<error>Spec '{$spec}' failed: {$reason}</error>");
        }

        $failCount = $this->scoreboard->getFailCount();

        $skipCount = $this->scoreboard->getSkipCount();

        $result = "{$this->scoreboard->getSpecCount()} specs, {$failCount} failed, {$skipCount} skipped";

        if ($failCount > 0) {
            $result = "<error>{$result}</error>";

        } elseif ($skipCount > 0) {
            $result = "<comment>{$result}</comment>";
            
        } else {
            $result = "<info>{$result}</info>";
        }

        $output->writeln("\n{$result}\n");
    }
}
