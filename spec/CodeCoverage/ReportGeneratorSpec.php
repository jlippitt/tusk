<?php

use Mockery as m;
use Tusk\CodeCoverage\ReportGenerator;

describe('ReportGenerator', function() {
    beforeEach(function() {
        $this->output = m::mock(
            'Tusk\CodeCoverage\Output\OutputInterface',
            ['getExtension' => 'html']
        );

        $this->invoker = m::mock('Tusk\Util\GlobalFunctionInvoker');

        $this->reportGenerator = new ReportGenerator(
            $this->output,
            $this->invoker
        );
    });

    afterEach(function() {
        m::close();
    });

    describe('generateReport', function() {
        it('should output report files', function() {
            $results = ['/foo/bar' => ['a'], '/baz/qux' => ['b']];

            $summaryOutput = '/foo/bar,/baz/qux';

            $this->invoker
                ->shouldReceive('dirname')
                ->with('/reports/index')
                ->once()
                ->ordered()
                ->andReturn('/reports')
            ;

            $this->invoker
                ->shouldReceive('is_dir')
                ->with('/reports')
                ->once()
                ->ordered()
                ->andReturn(false)
            ;

            $this->invoker
                ->shouldReceive('mkdir')
                ->with('/reports', 0777, true)
                ->ordered()
                ->once()
            ;


            $this->output
                ->shouldReceive('getSummaryOutput')
                ->with($results)
                ->once()
                ->andReturn($summaryOutput)
            ;

            $this->invoker
                ->shouldReceive('file_put_contents')
                ->with('/reports/index.html', $summaryOutput)
                ->once()
                ->ordered()
            ;

            $this->invoker
                ->shouldReceive('dirname')
                ->with('/reports/foo/bar')
                ->once()
                ->ordered()
                ->andReturn('/reports/foo')
            ;

            $this->invoker
                ->shouldReceive('is_dir')
                ->with('/reports/foo')
                ->once()
                ->ordered()
                ->andReturn(false)
            ;

            $this->invoker
                ->shouldReceive('mkdir')
                ->with('/reports/foo', 0777, true)
                ->ordered()
                ->once()
            ;

            $this->output
                ->shouldReceive('getFileOutput')
                ->with('/foo/bar', ['a'])
                ->once()
                ->andReturn('a')
            ;

            $this->invoker
                ->shouldReceive('file_put_contents')
                ->with('/reports/foo/bar.html', 'a')
                ->once()
                ->ordered()
            ;

            $this->invoker
                ->shouldReceive('dirname')
                ->with('/reports/baz/qux')
                ->once()
                ->ordered()
                ->andReturn('/reports/baz')
            ;

            $this->invoker
                ->shouldReceive('is_dir')
                ->with('/reports/baz')
                ->once()
                ->ordered()
                ->andReturn(true)
            ;

            $this->invoker
                ->shouldReceive('mkdir')
                ->with('/reports/baz', 0777, true)
                ->never()
            ;

            $this->output
                ->shouldReceive('getFileOutput')
                ->with('/baz/qux', ['b'])
                ->once()
                ->andReturn('b')
            ;

            $this->invoker
                ->shouldReceive('file_put_contents')
                ->with('/reports/baz/qux.html', 'b')
                ->once()
                ->ordered()
            ;

            $this->reportGenerator->generateReport(
                $results,
                '/reports'
            );
        });
    });
});
