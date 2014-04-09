<?php

use Tusk\Util\GlobalFunctionInvoker;

describe('GlobalFunctionInvoker', function() {
    it('should proxy all calls to built-in/global functions', function() {
        $invoker = new GlobalFunctionInvoker();
        expect($invoker->strpos('proxythis', 't'))->toBe(5);
    });
});
