<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PrettyPrint
{
  /**
   * @var string the query parameter
   */
  const queryParameter = 'pretty';

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

    if (method_exists($response, 'setEncodingOptions')) {
      if ((bool) $request->query(self::queryParameter)) {
        $response->setEncodingOptions(JSON_PRETTY_PRINT);
      }
    }

    return $response;
  }
}
