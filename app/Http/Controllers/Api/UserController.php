<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Domain\User\Exceptions\InvalidEmailVerificationTokenException;
use Domain\User\Exceptions\InvalidPasswordResetTokenException;
use Domain\User\Exceptions\PasswordResetTokenExpiredException;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Transformers\UserTransformer;
use Domain\User\UserService;
use Infrastructure\Exceptions\Http\BadRequestError;

class UserController extends Controller
{
  /**
   * @var \Domain\User\UserService
   */
  protected $userService;

  /**
   * @var \Domain\User\Transformers\UserTransformer
   */
  protected $userTransformer;

  /**
   * Create a new user controller instance
   *
   * @param \Domain\User\UserService $userService
   * @param \Domain\User\Transformers\UserTransformer $userTransformer
   */
  public function __construct(
    UserService $userService,
    UserTransformer $userTransformer
  ) {
    $this->userService = $userService;
    $this->userTransformer = $userTransformer;
  }

  /**
   * Retrieve an index of users.
   *
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\User\User>
   *
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
   */
  public function index()
  {
    $users = $this->userService->index(request()->query('limit'), request()->query('offset'))->setPath(route('api.user.index'));

    return fractal($users, $this->userTransformer);
  }

  /**
   * Create a new user.
   *
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
   */
  public function create()
  {
    $user = $this->userService->create([
      'email' => request()->input('email'),
      'password' => request()->input('password')
    ]);

    return response([], 201)
      ->header('Location', route('api.user.get', $user->id));
  }

  /**
   * Retrieve a user by id.
   *
   * @param integer $id
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function getById($id)
  {
    try {
      return fractal($this->userService->findById($id), $this->userTransformer);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('user.id.not_found')
        ]
      ]);
    }
  }

  /**
   * Retrieve a user by email.
   *
   * @param string $email
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function getByEmail($email)
  {
    try {
      return fractal($this->userService->findByEmail($email), $this->userTransformer)->toArray();
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('user.email.not_found')
        ]
      ]);
    }
  }

  /**
   * Retrieve an index of users matching a particular search phrase.
   *
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\User\User>
   *
   * @throws \Infrastructure\Exceptions\Http\BadRequestError
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
   */
  public function search()
  {
    switch (strtolower(request()->query('parameter'))) {
      case 'id':
      case 'email':
        break;
      default:
        throw (new BadRequestError())->setContext([
          'parameter' => [
            __('validation.regex', ['attribute' => 'parameter'])
          ]
        ]);
    }

    $users = $this->userService->search(
      request()->query('parameter'),
      request()->query('search'),
      (bool) request()->query('regex'),
      request()->query('limit'),
      request()->query('offset')
    )->setPath(route('api.user.search'));

    return fractal($users, $this->userTransformer);
  }

  /**
   * Update a user.
   *
   * @param integer $id
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\CannotUpdateUserException
   */
  public function update($id)
  {
    try {
      $user = $this->userService->findById($id);
      $this->authorize('user.update', $user);

      $user = $this->userService->update($user, [
        'password' => request()->input('password')
      ]);

      return fractal($user, $this->userTransformer);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('user.id.not_found')
        ]
      ]);
    }
  }

  /**
   * Router a change to the specified email.
   *
   * @param integer $id
   * @return \Illuminate\Http\JsonResponse
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\InvalidEmailException
   */
  public function requestEmailChange($id)
  {
    try {
      $user = $this->userService->findById($id);
      $this->authorize('user.update', $user);

      $this->userService->requestEmailChange($user, request()->input('email'));

      return response()->json([
        'message' => __('email.email_verification_sent')
      ], 202);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'id' => [
          __('user.id.not_found')
        ]
      ]);
    }
  }

  /**
   * Verify the user's email.
   *
   * @param string $email
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\InvalidEmailException
   * @throws \Domain\User\Exceptions\InvalidEmailVerificationTokenException
   */
  public function verifyEmail($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $user = $this->userService->verifyEmail($user, request()->query('email_verification_token'));

      return fractal($user, $this->userTransformer);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'email' => [
          __('user.email.not_found')
        ]
      ]);
    }
  }

  /**
   * Resend the user's email verification token.
   *
   * @param string $email
   * @return \Illuminate\Http\JsonResponse
   *
   * @throws \Domain\Exceptions\InvalidEmailVerificationTokenException
   * @throws \Domain\Exceptions\UserNotFoundException
   */
  public function resendEmailVerificationToken($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $this->userService->sendEmailVerificationToken($user);

      return response()->json([
        'message' => __('email.email_verification_resent')
      ], 202);
    } catch (InvalidEmailVerificationTokenException $e) {
      throw $e->setContext([
        'email_verification_token' => [
          __('user.exceptions.invalid_email_verification_token')
        ]
      ]);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'email' => [
          __('user.email.not_found')
        ]
      ]);
    }
  }

  /**
   * Send the user a password reset token.
   *
   * @param string $email
   * @return \Illuminate\Http\JsonResponse
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function forgotPassword($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $this->userService->forgotPassword($user);

      return response([
        'message' => __('passwords.sent')
      ], 202);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'email' => [
          __('user.email.not_found')
        ]
      ]);
    }
  }

  /**
   * Reset the user's password using the password reset token.
   *
   * @param string $email
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\PasswordResetTokenExpiredException
   * @throws \Domain\User\Exceptions\InvalidPasswordResetTokenException
   * @throws \Domain\User\Exceptions\InvalidPasswordException
   */
  public function resetPassword($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $user = $this->userService->resetPassword($user, request()->input('password'), request()->query('password_reset_token'));

      return fractal($user, $this->userTransformer);
    } catch (InvalidPasswordResetTokenException $e) {
      throw $e->setContext([
        'password_reset_token' => [
          __('user.exceptions.invalid_password_reset_token')
        ]
      ]);
    } catch (PasswordResetTokenExpiredException $e) {
      throw $e->setContext([
        'password_reset_token' => [
          __('user.exceptions.invalid_password_reset_token')
        ]
      ]);
    } catch (UserNotFoundException $e) {
      throw $e->setContext([
        'email' => [
          __('user.email.not_found')
        ]
      ]);
    }
  }
}
