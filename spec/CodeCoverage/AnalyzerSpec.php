<?php

use Mockery as m;
use Tusk\CodeCoverage\Analyzer;

describe('Analyzer', function() {
    beforeEach(function() {
        $this->filter = m::mock('PHP_CodeCoverage_Filter');

        $this->codeCoverage = m::mock('PHP_CodeCoverage', [
            'filter' => $this->filter,
            'start' => null,
            'stop' => null
        ]);

        $this->fileScanner = m::mock('Tusk\Util\FileScanner');

        $this->writerFactory = m::mock('Tusk\CodeCoverage\WriterFactory');

        $this->analyzer = new Analyzer(
            $this->codeCoverage,
            $this->fileScanner,
            $this->writerFactory
        );
    });

    afterEach(function() {
        m::close();
    });

    describe('begin()', function() {
        it('should start and stop code coverage', function() {
            $bodyCalled = false;
            $codeCoverageRunning = false;

            $this->codeCoverage
                ->shouldReceive('start')
                ->once()
                ->ordered()
                ->andReturnUsing(function() use (&$codeCoverageRunning) {
                    $codeCoverageRunning = true;
                })
            ;

            $body = function() use (&$bodyCalled, &$codeCoverageRunning) {
                expect($codeCoverageRunning)->toBe(true);
                $bodyCalled = true;
            };

            $this->codeCoverage
                ->shouldReceive('stop')
                ->once()
                ->ordered()
                ->andReturnUsing(function() use (&$codeCoverageRunning) {
                    $codeCoverageRunning = false;
                })
            ;

            $this->analyzer->begin(new \stdClass(), $body);

            expect($bodyCalled)->toBe(true);
        });

        it('should add files to whitelist', function() {
            $options = (object)[
                'whitelist' => ['a', 'b']
            ];

            $files = ['file1', 'file2', 'file3'];

            $this->fileScanner
                ->shouldReceive('find')
                ->with($options->whitelist, '.php')
                ->andReturn($files)
            ;

            $this->filter
                ->shouldReceive('addFilesToWhitelist')
                ->with($files)
                ->once()
            ;

            $this->analyzer->begin($options, function() {});
        });

        it('should add files to blacklist', function() {
            $options = (object)[
                'blacklist' => ['a', 'b']
            ];

            $files = ['file1', 'file2', 'file3'];

            $this->fileScanner
                ->shouldReceive('find')
                ->with($options->blacklist, '.php')
                ->andReturn($files)
            ;

            $this->filter
                ->shouldReceive('addFilesToBlacklist')
                ->with($files)
                ->once()
            ;

            $this->analyzer->begin($options, function() {});
        });

        it('should generate reports', function() {
            $options = (object)[
                'reports' => [
                    (object)[
                        'type' => 'html',
                        'location' => 'html_output_dir',
                        'generator' => 'foo'
                    ],
                    (object)[
                        'type' => 'clover',
                        'location' => 'clover_output.xml'
                    ]
                ]
            ];

            foreach ($options->reports as $report) {
                $writer = m::mock();

                $this->writerFactory
                    ->shouldReceive('create')
                    ->with($report)
                    ->once()
                    ->andReturn($writer)
                ;

                $writer
                    ->shouldReceive('process')
                    ->with($this->codeCoverage, $report->location)
                    ->once()
                ;
            }

            $this->analyzer->begin($options, function() {});
        });
    });
});
