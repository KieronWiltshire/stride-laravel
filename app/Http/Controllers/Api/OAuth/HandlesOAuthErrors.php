<?php

namespace App\Http\Controllers\Api\OAuth;

use Closure;
use Illuminate\Http\Response;
use \Laravel\Passport\Http\Controllers\ConvertsPsrResponses;

trait HandlesOAuthErrors
{
    use ConvertsPsrResponses;

    /**
     * Perform the given callback with exception handling.
     *
     * @param Closure $callback
     * @return Response
     */
    protected function withErrorHandling($callback)
    {
        // Ignore the original intent of creating a response
        return $callback();
    }
}
