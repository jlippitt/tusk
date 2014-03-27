<?php

namespace Tusk\CodeCoverage;

use Tusk\CodeCoverage\Output\OutputInterface;
use Tusk\Util\FileScanner;

/**
 * Code coverage analyzer
 *
 * @author James Lippitt
 */
class CodeCoverage
{
    private $analyzer;

    private $output;

    public function __construct(
        Analyzer $analyzer,
        OutputInterface $output
    ) {
        $this->analyzer = $analyzer;
        $this->output = $output;
    }

    public function begin(array $dirs, callable $body)
    {
        if (!ini_get('xdebug.coverage_enable')) {
            throw new \RuntimeException('Xdebug code coverage is not enabled');
        }

        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

        call_user_func($body);

        $results = xdebug_get_code_coverage();

        xdebug_stop_code_coverage();

        $results = $this->analyzer->analyze($dirs, $results);

        $outputDir = './code_coverage';

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        file_put_contents(
            "{$outputDir}/summary.{$this->output->getExtension()}",
            $this->output->getSummaryOutput($results)
        );

        foreach ($results as $file => $result) {
            $fileDir = $outputDir . dirname($file);

            if (!is_dir($fileDir)) {
                mkdir($fileDir, 0777, true);
            }

            file_put_contents(
                "{$fileDir}/" . basename($file) . ".{$this->output->getExtension()}",
                $this->output->getFileOutput($file, $result)
            );
        }
    }
}
