<?php

namespace Tusk\Util;

/**
 * This class simply invokes global PHP functions. By using this as a proxy for
 * calls to these functions, we can mock them when testing classes that need to
 * use them.
 *
 * @author James Lippitt
 */
class GlobalFunctionInvoker
{
    public function __call($method, array $args)
    {
        return call_user_func_array($method, $args);
    }
}
