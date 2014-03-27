<?php

namespace Tusk\CodeCoverage;

use Tusk\CodeCoverage\Output\OutputInterface;

class ReportGenerator
{
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function generateReport(array $results, $outputDir)
    {
        $this->writeFile(
            $outputDir . '/index',
            $this->output->getSummaryOutput($results)
        );

        foreach ($results as $file => $result) {
            $this->writeFile(
                $outputDir . $file,
                $this->output->getFileOutput($file, $result)
            );
        }
    }

    private function writeFile($file, $data)
    {
        $dir = dirname($file);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents("{$file}.{$this->output->getExtension()}", $data);
    }
}
