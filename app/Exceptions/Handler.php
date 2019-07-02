<?php

namespace App\Exceptions;

use Exception;
use Infrastructure\Exceptions\AppError;
use Infrastructure\Exceptions\Http\TooManyRequestsError;
use Infrastructure\Exceptions\Auth\AuthenticationFailedException;
use Domain\OAuth\Exceptions\InvalidClientException;
use Domain\OAuth\Exceptions\InvalidGrantException;
use Domain\OAuth\Exceptions\InvalidRefreshTokenException;
use Domain\OAuth\Exceptions\InvalidScopeException;
use Domain\OAuth\Exceptions\UnsupportedGrantTypeException;
use App\Exceptions\Request\InvalidRequestException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Infrastructure\Exceptions\Http\BadRequestError;
use Infrastructure\Exceptions\Http\ForbiddenError;
use Infrastructure\Exceptions\Http\InternalServerError;
use Infrastructure\Exceptions\Http\NotFoundError;
use Infrastructure\Exceptions\Http\UnauthorizedError;
use Infrastructure\Exceptions\Http\ValidationError;
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
    if (app()->environment(['local', 'development'])) {
      if ($request->has('dump')) {
        dd($exception);
      }
    }

    $conform = $this->conform($exception);
    $render = $conform->render();

    return response($render, $conform->getHttpStatus());
  }

  /**
   * Conform the exception to an application standard.
   * 
   * @param \Exception $exception
   * @return \Infrastructure\Exceptions\AppError
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
      $error = $this->conformHttpExceptionToAppError($exception);
    }

    /*
    |--------------------------------------------------------------------------
    | Handling AuthorizationException
    |--------------------------------------------------------------------------
    |
    | This default exception thrown by the Laravel framework does
    | not conform to the application's error response structure,
    | therefore we swap it out for an application defined error.
    |
    */
    if ($exception instanceof AuthorizationException) {
      $error = $this->conformAuthorizationExceptionToAppError($exception);
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
          $error = $this->conformHttpExceptionToAppError($exception);
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
   * Conform the http exception to an application standard error.
   *
   * @param \Symfony\Component\HttpKernel\Exception\HttpException $exception
   * @return \Infrastructure\Exceptions\AppError
   */
  public function conformHttpExceptionToAppError(HttpException $exception)
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

  /**
   * Conform the authorization exception to an application standard error.
   *
   * @param \Illuminate\Auth\Access\AuthorizationException $exception
   * @return \Infrastructure\Exceptions\AppError
   */
  public function conformAuthorizationExceptionToAppError(AuthorizationException $exception)
  {
    return new ForbiddenError();
  }
}
