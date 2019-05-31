<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\User\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;
use App\Exceptions\Http\BadRequestError;
use App\Exceptions\User\PasswordResetTokenExpiredException;
use App\Exceptions\User\InvalidPasswordResetTokenException;
use App\Entities\User;
use App\Exceptions\User\InvalidEmailVerificationTokenException;

class UserController extends Controller
{
  /**
   * @var UserRepository
   */
  private $users;

  /**
   * Create a new user controller instance
   *
   * @param App\Repositories\Contracts\UserRepository $users
   * @return void
   */
  public function __construct(UserRepository $users)
  {
    $this->users = $users;
  }

  /**
   * Retrieve an index of users.
   *
   * @return Illuminate\Http\JsonResponse
   */
  public function index()
  {
    return $this->users->all(request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.user.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);
  }

  /**
   * Create a new user.
   *
   * @return Illuminate\Http\JsonResponse
   */
  public function create()
  {
    return $this->users->create([
      'email' => request()->input('email'),
      'password' => request()->input('password')
    ]);
  }

  /**
   * Retrieve a user by id.
   *
   * @param integer $id
   * @return Illuminate\Http\JsonResponse
   */
  public function getById($id)
  {
    try {
      return $this->users->findById($id);
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
   * @return Illuminate\Http\JsonResponse
   */
  public function getByEmail($email)
  {
    try {
      return $this->users->findByEmail($email);
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
   * @return Illuminate\Http\JsonResponse
   *
   * @throws App\Exceptions\Http\BadRequestError
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

    return $this->users->find(request()->query('parameter'), request()->query('search'), (bool) request()->query('regex'), request()->query('limit'), request()->query('offset'))
      ->setPath(route('api.user.search'))
      ->setPageName('offset')
      ->appends([
        'limit' => request()->query('limit')
      ]);
  }

  /**
   * Update a user.
   *
   * @param integer $id
   * @return Illuminate\Http\JsonResponse
   */
  public function update($id)
  {
    try {
      $user = $this->users->findById($id);
      return $this->users->update($user, [
        'password' => request()->input('password')
      ]);
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
   * @return Illuminate\Http\JsonResponse
   */
  public function requestEmailChange($id)
  {
    try {
      $user = $this->users->findById($id);
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
   * @return Illuminate\Http\JsonResponse
   */
  public function verifyEmail($email)
  {
    try {
      $user = $this->users->findByEmail($email);
      return $this->users->verifyEmail($user, request()->query('email_verification_token'));
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
   * @return Illuminate\Http\JsonResponse
   *
   * @throws App\Exceptions\User\InvalidEmailVerificationTokenException
   * @throws App\Exceptions\User\UserNotFoundException
   */
  public function resendEmailVerificationToken($email)
  {
    try {
      $user = $this->users->findByEmail($email);
      $this->users->sendEmailVerificationToken($user);
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
   * @return Illuminate\Http\JsonResponse
   *
   * @throws App\Exceptions\User\UserNotFoundException
   */
  public function forgotPassword($email)
  {
    try {
      $user = $this->users->findByEmail($email);
      $this->users->forgotPassword($user);
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
   * @return Illuminate\Http\JsonResponse
   */
  public function resetPassword($email)
  {
    try {
      $user = $this->users->findByEmail($email);
      return $this->users->resetPassword($user, request()->input('password'), request()->query('password_reset_token'));
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
