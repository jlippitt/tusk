<?php

namespace Tusk\CodeCoverage;

use Tusk\Util\FileScanner;

class Analyzer
{
    private $fileScanner;

    public function __construct(FileScanner $fileScanner)
    {
        $this->fileScanner = $fileScanner;
    }

    public function analyze(array $dirs, array $coverage)
    {
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
            }
        }

        return $results;
    }
}
