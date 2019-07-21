<?php

namespace App\Http\Middleware;

use Closure;
use PragmaRX\Version\Package\Facade as Version;

class VersionHeader
{
  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure $next
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
