<?php

namespace Tusk\CodeCoverage;

use Tusk\Util\FileScanner;
use Tusk\Util\GlobalFunctionInvoker;

class Analyzer
{
    private $fileScanner;

    private $invoker;

    public function __construct(
        FileScanner $fileScanner,
        GlobalFunctionInvoker $invoker
    ) {
        $this->fileScanner = $fileScanner;
        $this->invoker = $invoker;
    }

    public function analyze(array $dirs, array $coverage)
    {
        $results = [];

        foreach ($this->fileScanner->find($dirs, '.php') as $file) {
            if (isset($coverage[$file])) {
                $results[$file] = [];

                foreach ($this->invoker->file($file) as $i => $line) {
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