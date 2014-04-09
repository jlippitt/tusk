<?php

use Mockery as m;
use Tusk\Util\FileScanner;

describe('FileScanner', function() {
    afterEach(function() {
        m::close();
    });

    describe('find()', function() {
        it('should scan directories recursively for files with the given ending', function() {
            $proxy = m::mock('Tusk\Util\GlobalFunctionProxy');
            $scanner = new FileScanner($proxy);

            $proxy->shouldReceive('is_dir')->andReturnUsing(function($dir) {
                return in_array($dir, ['a', 'b', 'a/a', 'a/b', 'b/a', 'b/b']);
            });

            $proxy->shouldReceive('scandir')->andReturnUsing(function($dir) {
                if (in_array($dir, ['a', 'b'])) {
                    return [
                        'a',
                        'b',
                        'c.foo',
                        'd.bar',
                        '.e.foo'
                    ];

                } else {
                    return [
                        'a.foo',
                        'b.bar',
                        'c.foo',
                        'd.bar',
                        '.e.foo'
                    ];
                }
            });

            $proxy->shouldReceive('realpath')->andReturnUsing(function($path) {
                return '/r/' . $path;
            });

            expect($scanner->find(['a', 'b'], '.foo'))->toBe([
                '/r/a/a/a.foo',
                '/r/a/a/c.foo',
                '/r/a/b/a.foo',
                '/r/a/b/c.foo',
                '/r/a/c.foo',
                '/r/b/a/a.foo',
                '/r/b/a/c.foo',
                '/r/b/b/a.foo',
                '/r/b/b/c.foo',
                '/r/b/c.foo'
            ]);
        });
    });
});
