<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Support\Exceptions\Auth\AuthenticationRequiredException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string
     *
     * @throws AuthenticationRequiredException
     */
    protected function redirectTo($request)
    {
        if ($request->is('api/*')) {
            throw new AuthenticationRequiredException();
        } else {
            return route('any');
        }
    }
}
