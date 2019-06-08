<?php

namespace App\Http\Controllers\OAuth;

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
    // Ignore the original intent of creating a response
    return $callback();
  }
}
