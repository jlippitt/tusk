<?php

use Mockery as m;
use Tusk\CodeCoverage\Analyzer;

describe('Analyzer', function() {
    beforeEach(function() {
        $this->fileScanner = m::mock('Tusk\Util\FileScanner');

        $this->invoker = m::mock('Tusk\Util\GlobalFunctionInvoker');

        $this->analyzer = new Analyzer($this->fileScanner, $this->invoker);
    });

    afterEach(function() {
        m::close();
    });

    describe('analyze()', function() {
        it('should return line by line code and coverage details for each file', function() {
            $dirs = ['src', 'more_src'];

            $files = ['a', 'c'];

            $coverage = [
                'a' => [1 => 1, 3 => -1],
                'b' => [2 => 1, 4 => 1],
                'c' => [2 => 1, 4 => -2]
            ];

            $this->fileScanner
                ->shouldReceive('find')
                ->with($dirs, '.php')
                ->andReturn($files)
            ;

            $this->invoker
                ->shouldReceive('file')
                ->with('a')
                ->andReturn(['hello', 'world', '!'])
            ;

            $this->invoker
                ->shouldReceive('file')
                ->with('c')
                ->andReturn(['this', 'is', 'code', 'coverage'])
            ;

            expect($this->analyzer->analyze($dirs, $coverage))->toBe([
                'a' => [
                    ['hello', 1],
                    ['world', 0],
                    ['!', -1]
                ],
                'c' => [
                    ['this', 0],
                    ['is', 1],
                    ['code', 0],
                    ['coverage', -2]
                ]
            ]);
        });
    });
});
