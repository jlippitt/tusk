<?php

namespace Tusk;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The default console command. Runs specs based on command line arguments and
 * then displays the results.
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Command extends BaseCommand
{
    /**
     * @param Scoreboard $scoreboard
     */
    public function __construct(SpecRunner $specRunner, Scoreboard $scoreboard)
    {
        parent::__construct();
        $this->specRunner = $specRunner;
        $this->scoreboard = $scoreboard;
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($input->getArgument('files') as $file) {
            require($file);
        }

        $this->specRunner->run();

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
