<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\AppError;
use Illuminate\Validation\ValidationException;
use App\Exceptions\Http\BadRequestError;
use App\Exceptions\Http\ForbiddenError;
use App\Exceptions\Http\InternalServerError;
use App\Exceptions\Http\NotFoundError;
use App\Exceptions\Http\UnauthorizedError;
use App\Exceptions\Http\ValidationError;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @throws Exception
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
    $exception = $this->conform($exception);

    return response([
      'error' => $exception->render()
    ], $exception->getHttpStatus());
  }

  /**
   * Conform the exception to an application standard.
   * 
   * @param Exception $exception
   * @return App\Exceptions\AppException
   */
  public function conform($exception)
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
      switch ($exception->getStatusCode()) {
        case 400:
          $error = new BadRequestError();
          break;
        case 401:
          $error = new UnauthorizedError();
          break;
        case 403:
          $error = new ForbiddenError();
          break;
        case 404:
          $error = new NotFoundError();
          break;
        case 422:
          $error = new ValidationError();
          break;
        case 500:
        default:
          $error = new InternalServerError();
          break;
      }
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

      if (app()->environment(['local', 'development'])) {
        $error->setContext([
          'exception' => $this->convertExceptionToArray($exception)
        ]);
      }
    }

    return $error;
  }
}
