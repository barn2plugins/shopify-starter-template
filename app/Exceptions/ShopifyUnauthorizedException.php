<?php

namespace Barn2App\Exceptions;

use Exception;

class ShopifyUnauthorizedException extends Exception
{
    public function render($request)
    {
        return response()->view('errors.401', [], 401);
    }
}
