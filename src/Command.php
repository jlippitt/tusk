<?php

namespace Tusk;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tusk\CodeCoverage\CodeCoverage;
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

    private $codeCoverage;

    /**
     * @param Scoreboard $scoreboard
     */
    public function __construct(
        FileScanner $fileScanner,
        SpecRunner $specRunner,
        CodeCoverage $codeCoverage
    ) {
        parent::__construct();
        $this->fileScanner = $fileScanner;
        $this->specRunner = $specRunner;
        $this->codeCoverage = $codeCoverage;
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
                InputArgument::IS_ARRAY,
                'Path(s) to spec file(s)'
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_file('./tusk.json')) {
            $options = @json_decode(file_get_contents('./tusk.json'));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(json_last_error_msg());
            }

        } else {
            $options = new \stdClass();
        }

        if (!empty($input->getArgument('files'))) {
            $options->paths = $input->getArgument('files');
        }

        if (isset($options->bootstrap)) {
            require($options->bootstrap);
        }

        foreach ($this->fileScanner->find($options->paths, 'Spec.php') as $file) {
            require($file);
        }

        if (isset($options->codeCoverage)) {
            $this->codeCoverage->begin(
                $options->codeCoverage,
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
