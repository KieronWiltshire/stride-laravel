<?php

namespace App\Http\Controllers\Api\OAuth;

use \Laravel\Passport\Http\Controllers\ConvertsPsrResponses;

trait HandlesOAuthErrors
{
  use ConvertsPsrResponses;

  /**
   * Perform the given callback with exception handling.
   *
   * @param \Closure $callback
   * @return \Illuminate\Http\Response
   */
  protected function withErrorHandling($callback)
  {
    return $callback();
  }
}
