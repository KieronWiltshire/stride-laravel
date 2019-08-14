<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PragmaRX\Version\Package\Facade as Version;

class VersionHeader
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->add([
            'X-Version' => Version::compact()
        ]);

        return $response;
    }
}
