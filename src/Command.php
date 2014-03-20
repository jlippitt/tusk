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
    private $specRunner;

    /**
     * @param Scoreboard $scoreboard
     */
    public function __construct(SpecRunner $specRunner)
    {
        parent::__construct();
        $this->specRunner = $specRunner;
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

        foreach ($this->specRunner->getFailedSpecs() as $spec => $reason) {
            $output->writeln("<error>Spec '{$spec}' failed: {$reason}</error>\n");
        }

        $failCount = $this->specRunner->getFailCount();

        $skipCount = $this->specRunner->getSkipCount();

        $result = "{$this->specRunner->getSpecCount()} specs, {$failCount} failed, {$skipCount} skipped";

        if ($failCount > 0) {
            $result = "<error>{$result}</error>";

        } elseif ($skipCount > 0) {
            $result = "<comment>{$result}</comment>";
            
        } else {
            $result = "<info>{$result}</info>";
        }

        $output->writeln("{$result}\n");
    }
}
