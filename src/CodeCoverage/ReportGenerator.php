<?php

namespace Tusk\CodeCoverage;

use Tusk\CodeCoverage\Output\OutputInterface;
use Tusk\Util\GlobalFunctionProxy;

class ReportGenerator
{
    public function __construct(
        OutputInterface $output,
        GlobalFunctionProxy $proxy
    ) {
        $this->output = $output;
        $this->invoker = $proxy;
    }

    public function generateReport(array $results, $outputDir)
    {
        $this->writeFile(
            $outputDir . '/index',
            $this->output->getSummaryOutput($results)
        );

        foreach ($results['files'] as $file => $result) {
            $this->writeFile(
                $outputDir . $file,
                $this->output->getFileOutput($file, $result)
            );
        }
    }

    private function writeFile($file, $data)
    {
        $dir = $this->invoker->dirname($file);

        if (!$this->invoker->is_dir($dir)) {
            $this->invoker->mkdir($dir, 0777, true);
        }

        $this->invoker->file_put_contents(
            "{$file}.{$this->output->getExtension()}",
            $data
        );
    }
}
