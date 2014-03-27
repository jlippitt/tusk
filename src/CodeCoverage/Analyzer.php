<?php

namespace Tusk\CodeCoverage;

use Tusk\CodeCoverage\Output\OutputInterface;
use Tusk\Util\FileScanner;

/**
 * Code coverage analyzer
 *
 * @author James Lippitt
 */
class Analyzer
{
    private $fileScanner;

    public function __construct(
        FileScanner $fileScanner,
        OutputInterface $output
    ) {
        $this->fileScanner = $fileScanner;
        $this->output = $output;
    }

    public function analyze(array $dirs, callable $body)
    {
        if (!ini_get('xdebug.coverage_enable')) {
            throw new \RuntimeException('Xdebug code coverage is not enabled');
        }

        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

        call_user_func($body);

        $coverage = xdebug_get_code_coverage();

        xdebug_stop_code_coverage();

        $results = [];

        foreach ($this->fileScanner->find($dirs, '.php') as $file) {
            if (isset($coverage[$file])) {
                $results[$file] = [];

                foreach (file($file) as $i => $line) {
                    $results[$file][] = [
                        $line,
                        isset($coverage[$file][$i + 1]) ? $coverage[$file][$i + 1] : 0
                    ];
                }

                unset($coverage[$file]);
            }
        }

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
