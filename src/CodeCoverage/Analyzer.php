<?php

namespace Tusk\CodeCoverage;

use Tusk\Util\FileScanner;

class Analyzer
{
    private $codeCoverageFactory;
    private $fileScanner;
    private $writerFactory;

    public function __construct(
        callable $codeCoverageFactory,
        FileScanner $fileScanner,
        WriterFactory $writerFactory
    ) {
        $this->codeCoverageFactory = $codeCoverageFactory;
        $this->fileScanner = $fileScanner;
        $this->writerFactory = $writerFactory;
    }

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
