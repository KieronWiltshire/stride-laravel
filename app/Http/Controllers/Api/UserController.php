<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\User\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Contracts\Repositories\UserRepository;
use App\Exceptions\Http\BadRequestError;
use App\Exceptions\User\PasswordResetTokenExpiredException;
use App\Exceptions\User\InvalidPasswordResetTokenException;
use App\Exceptions\User\InvalidEmailVerificationTokenException;
use App\Transformers\UserTransformer;

class UserController extends Controller
{
  /**
   * @var \App\Contracts\Repositories\UserRepository
   */
  private $userRepository;

  /**
   * @var \App\Transformers\UserTransformer
   */
  private $userTransformer;

  /**
   * Create a new user controller instance
   *
   * @param \App\Contracts\Repositories\UserRepository $userRepository
   * @param \App\Transformers\UserTransformer $userTransformer
   */
  public function __construct(
    UserRepository $userRepository,
    UserTransformer $userTransformer
  ) {
    $this->userRepository = $userRepository;
    $this->userTransformer = $userTransformer;
  }

  /**
   * Retrieve an index of users.
   *
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\User>
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  public function index()
  {
    $users = $this->userRepository->allAsPaginated(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.user.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return fractal($users, $this->userTransformer)->toArray();
  }

  /**
   * Create a new user.
   *
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\CannotCreateUserException
   */
  public function create()
  {
    $user = $this->userRepository->create([
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
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  public function getById($id)
  {
    try {
      return fractal($this->userRepository->findById($id), $this->userTransformer)->toArray();
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
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  public function getByEmail($email)
  {
    try {
      return fractal($this->userRepository->findByEmail($email), $this->userTransformer)->toArray();
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Entities\User>
   *
   * @throws \App\Exceptions\Http\BadRequestError
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
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

    $users = $this->userRepository->findAsPaginated(request()->query('parameter'), request()->query('search'), (bool) request()->query('regex'), request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.user.search'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);

    return fractal($users, $this->userTransformer)->toArray();
  }

  /**
   * Update a user.
   *
   * @param integer $id
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\User\CannotUpdateUserException
   */
  public function update($id)
  {
    try {
      $user = $this->userRepository->findById($id);
      $this->authorize('user.update', $user);

      $user = $this->userRepository->update($user, [
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
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\User\InvalidEmailException
   */
  public function requestEmailChange($id)
  {
    try {
      $user = $this->userRepository->findById($id);
      $this->authorize('user.update', $user);

      if ($this->users->requestEmailChange($user, request()->input('email'))) {
        return response()->json([
          'message' => __('email.email_verification_sent')
        ], 202);
      }
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
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\User\InvalidEmailException
   * @throws \App\Exceptions\User\InvalidEmailVerificationTokenException
   */
  public function verifyEmail($email)
  {
    try {
      $user = $this->userRepository->findByEmail($email);
      $user = $this->userRepository->verifyEmail($user, request()->query('email_verification_token'));

      return fractal($user, $this->userTransformer)->toArray();
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
   * @throws \App\Exceptions\User\InvalidEmailVerificationTokenException
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  public function resendEmailVerificationToken($email)
  {
    try {
      $user = $this->userRepository->findByEmail($email);
      $this->userRepository->sendEmailVerificationToken($user);

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
   * @throws \App\Exceptions\User\UserNotFoundException
   */
  public function forgotPassword($email)
  {
    try {
      $user = $this->userRepository->findByEmail($email);
      $this->userRepository->forgotPassword($user);

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
   * @return \App\Entities\User
   *
   * @throws \App\Exceptions\User\UserNotFoundException
   * @throws \App\Exceptions\User\PasswordResetTokenExpiredException
   * @throws \App\Exceptions\User\InvalidPasswordResetTokenException
   * @throws \App\Exceptions\User\InvalidPasswordException
   */
  public function resetPassword($email)
  {
    try {
      $user = $this->userRepository->findByEmail($email);
      $user = $this->userRepository->resetPassword($user, request()->input('password'), request()->query('password_reset_token'));

      return fractal($user, $this->userTransformer)->toArray();
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
