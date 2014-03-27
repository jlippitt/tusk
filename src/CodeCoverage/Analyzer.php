<?php

namespace Tusk\CodeCoverage;

use Tusk\Util\FileScanner;
use Tusk\Util\GlobalFunctionInvoker;

class Analyzer
{
    const EXECUTED = 1;
    const NO_DATA = 0;
    const NOT_EXECUTED = -1;
    const NOT_EXECUTABLE = -2;

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
        $results = [
            'stats' => [
                'totalLines' => 0,
                'executableLines' => 0,
                'linesExecuted' => 0
            ],
            'files' => []
        ];

        foreach ($this->fileScanner->find($dirs, '.php') as $file) {
            if (!isset($coverage[$file])) {
                continue;
            }

            $result = [
                'stats' => [
                    'totalLines' => 0,
                    'executableLines' => 0,
                    'linesExecuted' => 0
                ],
                'lines' => []
            ];

            foreach ($this->invoker->file($file) as $i => $line) {
                $status = isset($coverage[$file][$i + 1]) ?
                    $coverage[$file][$i + 1] : self::NO_DATA;

                $result['lines'][] = [$line, $status];

                ++$result['stats']['totalLines'];

                if ($status === self::EXECUTED || $status === self::NOT_EXECUTED) {
                    ++$result['stats']['executableLines'];

                    if ($status === self::EXECUTED) {
                        ++$result['stats']['linesExecuted'];
                    }
                }
            }

            $result['stats']['coverage'] = $result['stats']['linesExecuted'] /
                (float)$result['stats']['executableLines'];

            $results['files'][$file] = $result;

            $results['stats']['totalLines'] += $result['stats']['totalLines'];
            $results['stats']['executableLines'] += $result['stats']['executableLines'];
            $results['stats']['linesExecuted'] += $result['stats']['linesExecuted'];
        }

        $results['stats']['coverage'] = $results['stats']['linesExecuted'] /
            (float)$results['stats']['executableLines'];

        return $results;
    }
}
