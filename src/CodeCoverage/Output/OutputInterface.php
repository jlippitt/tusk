<?php

namespace Tusk\CodeCoverage\Output;

interface OutputInterface
{
    public function getExtension();

    public function getSummaryOutput(array $results);

    public function getFileOutput($file, array $result);
}
