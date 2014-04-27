<?php

namespace Tusk\CodeCoverage\Driver;

class Xdebug implements DriverInterface
{
    public function __construct()
    {
        if (!ini_get('xdebug.coverage_enable')) {
            throw new \RuntimeException('Xdebug code coverage is not enabled');
        }
    }

    public function start()
    {
        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    }

    public function stop()
    {
        $results = xdebug_get_code_coverage();

        xdebug_stop_code_coverage();

        return $results;
    }
}
