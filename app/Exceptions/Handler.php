<?php

namespace App\Exceptions;

use App\Exceptions\Http\TooManyRequestsError;
use Exception;
use App\Exceptions\Auth\AuthenticationFailedException;
use App\Exceptions\OAuth\InvalidClientException;
use App\Exceptions\OAuth\InvalidGrantException;
use App\Exceptions\OAuth\InvalidRefreshTokenException;
use App\Exceptions\OAuth\InvalidScopeException;
use App\Exceptions\OAuth\UnsupportedGrantTypeException;
use App\Exceptions\Request\InvalidRequestException;
use Illuminate\Validation\ValidationException;
use App\Exceptions\Http\BadRequestError;
use App\Exceptions\Http\ForbiddenError;
use App\Exceptions\Http\InternalServerError;
use App\Exceptions\Http\NotFoundError;
use App\Exceptions\Http\UnauthorizedError;
use App\Exceptions\Http\ValidationError;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use League\OAuth2\Server\Exception\OAuthServerException;

class Handler extends ExceptionHandler
{
  /**
   * A list of the exception types that are not reported.
   *
   * @var array
   */
  protected $dontReport = [
    //
  ];

  /**
   * A list of the inputs that are never flashed for validation exceptions.
   *
   * @var array
   */
  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  /**
   * Report or log an exception.
   *
   * @param \Exception $exception
   * @return void
   * @throws \Exception
   */
  public function report(Exception $exception)
  {
    parent::report($exception);
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Exception  $exception
   * @return \Illuminate\Http\Response
   */
  public function render($request, Exception $exception)
  {
    dd($exception);
    $conform = $this->conform($exception);
    $render = $conform->render();

    if (app()->environment(['local', 'development'])) {
      $render['stack'] = $exception->getTrace();
    }

    return response($render, $conform->getHttpStatus());
  }

  /**
   * Conform the exception to an application standard.
   * 
   * @param \Exception $exception
   * @return \App\Exceptions\AppError
   */
  public function conform(Exception $exception)
  {
    $error = null;

    /*
    |--------------------------------------------------------------------------
    | Handling HttpException
    |--------------------------------------------------------------------------
    |
    | This default exception thrown by the Laravel framework does
    | not conform to the application's error response structure,
    | therefore we swap it out for an application defined error.
    |
    */
    if ($exception instanceof HttpException) {
      $error = $this->conformExceptionToHttpError($exception);
    }

    /*
    |--------------------------------------------------------------------------
    | Handling ValidationException
    |--------------------------------------------------------------------------
    |
    | This default exception thrown by the Laravel framework does
    | not conform to the application's error response structure,
    | therefore we swap it out for an application defined error.
    |
    */
    if ($exception instanceof ValidationException) {
      $error = new ValidationError();
      $error->setContext($exception->errors());
    }

    /*
    |--------------------------------------------------------------------------
    | Handling OAuthServerException
    |--------------------------------------------------------------------------
    |
    | This default exception thrown by the Laravel framework does
    | not conform to the application's error response structure,
    | therefore we swap it out for an application defined error.
    |
    */
    if ($exception instanceof OAuthServerException) {
      switch ($exception->getCode()) {
        case 2:
          $error = new UnsupportedGrantTypeException();
          break;
        case 3:
          $error = new InvalidRequestException();
          break;
        case 4:
          $error = new InvalidClientException();
          break;
        case 5:
          $error = new InvalidScopeException();
          break;
        case 6:
          $error = new AuthenticationFailedException();
          break;
        case 8:
          $error = new InvalidRefreshTokenException();
          break;
        case 10:
          $error = new InvalidGrantException();
          break;
        default:
          $error = $this->conformExceptionToHttpError($exception);
          break;
      }
    }

    /*
    |--------------------------------------------------------------------------
    | Handling AppError
    |--------------------------------------------------------------------------
    |
    | Use the AppError in the response.
    |
    */
    if ($exception instanceof AppError) {
      $error = $exception;
    }

    if (!$error) {
      $error = new InternalServerError();
    }

    return $error;
  }

  /**
   * Conform the exception to an application standard HTTP error.
   *
   * @param \Symfony\Component\HttpKernel\Exception\HttpException $exception
   * @return \App\Exceptions\AppError
   */
  public function conformExceptionToHttpError(HttpException $exception)
  {
    switch ($exception->getStatusCode()) {
      case 400:
        return new BadRequestError();
      case 401:
        return new UnauthorizedError();
      case 403:
        return new ForbiddenError();
      case 404:
        return new NotFoundError();
      case 422:
        return new ValidationError();
      case 429:
        $headers = $exception->getHeaders();

        return (new TooManyRequestsError(null))->setContext([
          'limit' => $headers['X-RateLimit-Limit'],
          'remaining' => $headers['X-RateLimit-Remaining'],
          'retryAfter' => $headers['Retry-After'],
          'resetAt' => $headers['X-RateLimit-Reset']
        ]);
      case 500:
      default:
        return new InternalServerError();
    }
  }
}
