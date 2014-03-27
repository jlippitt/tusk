<?php

namespace Tusk\CodeCoverage\Output;

class Html implements OutputInterface
{
    public function getExtension()
    {
        return 'html';
    }

    public function getSummaryOutput(array $results)
    {
        ob_start();
        require(__DIR__ . '/templates/summary.phtml');
        return ob_get_clean();
    }

    public function getFileOutput($file, array $result)
    {
        ob_start();
        require(__DIR__ . '/templates/file.phtml');
        return ob_get_clean();
    }
}
