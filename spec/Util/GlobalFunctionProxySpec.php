<?php

use Tusk\Util\GlobalFunctionProxy;

describe('GlobalFunctionProxy', function() {
    it('should proxy all calls to built-in/global functions', function() {
        $proxy = new GlobalFunctionProxy();
        expect($proxy->strpos('proxythis', 't'))->toBe(5);
    });
});
