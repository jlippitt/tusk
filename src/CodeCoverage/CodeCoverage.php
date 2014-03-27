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

    private $reportGenerator;

    public function __construct(
        Analyzer $analyzer,
        ReportGenerator $reportGenerator
    ) {
        $this->analyzer = $analyzer;
        $this->reportGenerator = $reportGenerator;
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

        $this->reportGenerator->generateReport(
            $results,
            './code_coverage'
        );
    }
}
