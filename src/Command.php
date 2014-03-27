<?php

namespace Tusk;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tusk\CodeCoverage\Analyzer;
use Tusk\Util\FileScanner;

/**
 * The default console command. Runs specs based on command line arguments and
 * then displays the results.
 *
 * @author James Lippitt <james.lippitt@gmail.com>
 */
class Command extends BaseCommand
{
    private $fileScanner;

    private $specRunner;

    private $analyzer;

    /**
     * @param Scoreboard $scoreboard
     */
    public function __construct(
        FileScanner $fileScanner,
        SpecRunner $specRunner,
        Analyzer $analyzer
    ) {
        parent::__construct();
        $this->fileScanner = $fileScanner;
        $this->specRunner = $specRunner;
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('tusk')
            ->setDescription('Run specs')
            ->addArgument(
                'files',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Path(s) to spec file(s)'
            )
            ->addOption(
                'code-coverage-include',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Enables code coverage'
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->fileScanner->find($input->getArgument('files'), 'Spec.php') as $file) {
            require($file);
        }

        if ($input->getOption('code-coverage-include')) {
            $this->analyzer->analyze(
                $input->getOption('code-coverage-include'),
                [$this->specRunner, 'run']
            );

        } else {
            $this->specRunner->run();
        }

        foreach ($this->specRunner->getFailedSpecs() as $spec => $exception) {
            $output->writeln("<error>Spec '{$spec}' failed: {$exception->getMessage()}</error>\n");
            $output->writeln("{$exception->getTraceAsString()}\n");
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

        return $failCount > 0 ? 1 : 0;
    }
}
