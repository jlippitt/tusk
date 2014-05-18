<?php

namespace Tusk\CodeCoverage;

/**
 * Constructs PHP_CodeCoverage report writers from configuration parameters
 *
 * @author James Lippitt
 */
class WriterFactory
{
    /**
     * Construct a report writer from the given configuration
     *
     * @param \stdClass $options
     * @return object
     */
    public function create(\stdClass $options)
    {
        switch ($options->type) {
            case 'clover':
                return new \PHP_CodeCoverage_Report_Clover();
            case 'crap4j':
                return new \PHP_CodeCoverage_Report_Crap4j();
            case 'html':
                return new \PHP_CodeCoverage_Report_HTML(
                    isset($options->lowUpperBound) ? $options->lowUpperBound : 50,
                    isset($options->highUpperBound) ? $options->highUpperBound : 90,
                    isset($options->generator) ? $options->generator : ''
                );
            case 'php':
                return new \PHP_CodeCoverage_Report_PHP();
            case 'text':
                return new \PHP_CodeCoverage_Report_Text(
                    $options->lowUpperBound,
                    $options->highUpperBound,
                    $options->showUncoveredFiles,
                    $options->showOnlySummary
                );
            case 'xml':
                return new \PHP_CodeCoverage_Report_XML();
            default:
                throw new \InvalidArgumentException(
                    "Unsupported report type: {$options->type}"
                );
        }
    }
}
