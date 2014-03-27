<?php

namespace Tusk\CodeCoverage;

use Tusk\CodeCoverage\Output\OutputInterface;
use Tusk\Util\GlobalFunctionInvoker;

class ReportGenerator
{
    public function __construct(
        OutputInterface $output,
        GlobalFunctionInvoker $invoker
    ) {
        $this->output = $output;
        $this->invoker = $invoker;
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
