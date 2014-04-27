<?php

namespace Tusk\CodeCoverage;

use Tusk\CodeCoverage\Driver\Driverinterface;
use Tusk\Util\FileScanner;

/**
 * Code coverage analyzer
 *
 * @author James Lippitt
 */
class CodeCoverage
{
    private $driver;

    private $analyzer;

    private $reportGenerator;

    private $fileScanner;

    public function __construct(
        DriverInterface $driver,
        Analyzer $analyzer,
        ReportGenerator $reportGenerator,
        FileScanner $fileScanner
    ) {
        $this->driver = $driver;
        $this->analyzer = $analyzer;
        $this->reportGenerator = $reportGenerator;
        $this->fileScanner = $fileScanner;
    }

    public function begin(\stdClass $config, callable $body)
    {
        $files = $this->fileScanner->find($config->sourcePaths, '.php');

        // Initial code coverage run over our specs
        $this->driver->start();

        call_user_func($body);

        $results = $this->driver->stop();

        // For files in the source directory that were not loaded, there will
        // be no information in the report. We need to artificially generate
        // this information.
        foreach ($files as $file) {
            if (isset($results[$file])) {
                continue;
            }

            $this->driver->start();

            require_once($file);

            $coverage = $this->driver->stop();

            if (!isset($coverage[$file])) {
                // It likely had no executable lines, e.g. an interface
                continue;
            }

            foreach ($coverage[$file] as &$lineStatus) {
                // Some lines will execute when we load the file, which is a
                // false positive. Reverse this!
                if ($lineStatus === Analyzer::EXECUTED) {
                    $lineStatus = Analyzer::NOT_EXECUTED;
                }
            }

            $results[$file] = $coverage[$file];
        }

        $results = $this->analyzer->analyze($files, $results);

        $this->reportGenerator->generateReport(
            $results,
            $config->outputDirectory
        );
    }
}
