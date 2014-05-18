<?php

use Tusk\CodeCoverage\WriterFactory;

describe('WriterFactory', function() {
    beforeEach(function() {
        $this->factory = new WriterFactory();
    });

    describe('create()', function() {
        it('should create the correct code coverage report writer type', function() {
            $types = [
                'clover' => 'PHP_CodeCoverage_Report_Clover',
                'crap4j' => 'PHP_CodeCoverage_Report_Crap4j',
                'html' => 'PHP_CodeCoverage_Report_HTML',
                'php' => 'PHP_CodeCoverage_Report_PHP',
                'text' => 'PHP_CodeCoverage_Report_Text',
                'xml' => 'PHP_CodeCoverage_Report_XML',
            ];

            // Need some additional parameters that are required by text report
            // type
            $options = [
                'lowUpperBound' => 50,
                'highUpperBound' => 90,
                'showUncoveredFiles' => true,
                'showOnlySummary' => false
            ];

            foreach ($types as $type => $class) {
                $writer = $this->factory->create(
                    (object)array_merge(['type' => $type], $options)
                );

                expect($writer)->toBeInstanceOf($class);
            }
        });

        it('should throw an exception if an unknown writer type is requested', function() {
            expect(
                function() {
                    $this->factory->create((object)['type' => 'unknown']);
                }
            )->toThrow('InvalidArgumentException');
        });
    });
});
