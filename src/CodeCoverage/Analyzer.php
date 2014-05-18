<?php

namespace Tusk\CodeCoverage;

use Tusk\Util\FileScanner;

class Analyzer
{
    private $codeCoverage;
    private $fileScanner;
    private $writerFactory;

    public function __construct(
        \PHP_CodeCoverage $codeCoverage,
        FileScanner $fileScanner,
        WriterFactory $writerFactory
    ) {
        $this->codeCoverage = $codeCoverage;
        $this->fileScanner = $fileScanner;
        $this->writerFactory = $writerFactory;
    }

    public function begin(\stdClass $options, callable $body)
    {
        $this->addFilesToFilter($options, 'whitelist');
        $this->addFilesToFilter($options, 'blacklist');

        $this->codeCoverage->start('Spec');

        call_user_func($body);

        $this->codeCoverage->stop();

        if (isset($options->reports)) {
            foreach ($options->reports as $report) {
                $writer = $this->writerFactory->create($report);
                $writer->process($this->codeCoverage, $report->location);
            }
        }
    }

    private function addFilesToFilter(\stdClass $options, $list)
    {
        if (!isset($options->$list)) {
            return;
        }

        $filter = $this->codeCoverage->filter();
        $method = 'addFileTo' . ucfirst($list);

        foreach ($this->fileScanner->find($options->$list, '.php') as $file) {
            $filter->$method($file);
        }
    }
}
