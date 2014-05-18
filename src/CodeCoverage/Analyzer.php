<?php

namespace Tusk\CodeCoverage;

use Tusk\Util\FileScanner;

/**
 * Conducts PHP_CodeCoverage reports, handling the stopping and starting of
 * code coverage, adding filters and generating reports.
 *
 * @author James Lippitt
 */
class Analyzer
{
    private $codeCoverageFactory;
    private $fileScanner;
    private $writerFactory;

    /**
     * @param callable $codeCoverageFactory
     * @param FileScanner $fileScanner
     * @param WriterFactory $writerFactory
     */
    public function __construct(
        callable $codeCoverageFactory,
        FileScanner $fileScanner,
        WriterFactory $writerFactory
    ) {
        $this->codeCoverageFactory = $codeCoverageFactory;
        $this->fileScanner = $fileScanner;
        $this->writerFactory = $writerFactory;
    }

    /**
     * Wraps the running of specs with code coverage generation and reporting
     *
     * @param \stdClass $options Code coverage filter and report configuration
     * @param callable $body Function called to run the specs
     */
    public function begin(\stdClass $options, callable $body)
    {
        // Lazy-load code coverage. This means an error won't be raised if
        // PHP_CodeCoverage is not installed, but code coverage is not enabled
        // (which is allowed).
        $codeCoverage = call_user_func($this->codeCoverageFactory);

        foreach (['whitelist', 'blacklist'] as $list) {
            if (isset($options->$list)) {
                $codeCoverage
                    ->filter()
                    ->{'addFilesTo' . ucfirst($list)}(
                        $this->fileScanner->find($options->$list, '.php')
                    )
                ;
            }
        }

        $codeCoverage->start('Spec');

        call_user_func($body);

        $codeCoverage->stop();

        if (isset($options->reports)) {
            foreach ($options->reports as $report) {
                $writer = $this->writerFactory->create($report);
                $writer->process($codeCoverage, $report->location);
            }
        }
    }
}
