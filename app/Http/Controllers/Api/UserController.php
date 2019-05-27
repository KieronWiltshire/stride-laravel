<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;
use App\Exceptions\Http\BadRequestError;
use App\Exceptions\Http\NotFoundError;
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
   * @param Illuminate\Http\Request $request
   * @return Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    return $this->users->all($request->query('limit'), $request->query('offset'))
      ->setPath(route('user.index'))
      ->setPageName('offset')
      ->appends([
        'limit' => $request->query('limit')
      ]);
  }

  /**
   * Create a new user.
   * 
   * @param Illuminate\Http\Request $request
   * @return Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    return $this->users->create([
      'email' => $request->input('email'),
      'password' => $request->input('password')
    ]);
  }

  /**
   * Retrieve a user by id.
   * 
   * @param integer $id
   * @return Illuminate\Http\Response
   */
  public function getById($id)
  {
    return $this->users->findById($id);
  }

  /**
   * Retrieve a user by email.
   * 
   * @param string $email
   * @return Illuminate\Http\Response
   */
  public function getByEmail($email)
  {
    return $this->users->findByEmail($email);
  }

  /**
   * Retrieve an index of users matching a particular search phrase.
   * 
   * @param Illuminate\Http\Request $request
   * @return Illuminate\Http\Response
   */
  public function search(Request $request)
  {
    switch (strtolower($request->query('parameter'))) {
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

    return $this->users->find($request->query('parameter'), $request->query('search'), (bool)$request->query('regex'), $request->query('limit'), $request->query('offset'))
      ->setPath(route('user.search'))
      ->setPageName('offset')
      ->appends([
        'limit' => $request->query('limit')
      ]);
  }

  /**
   * Update a user.
   * 
   * @param Illuminate\Http\Request $request
   * @param integer $id
   * @return Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $user = $this->users->findById($id);

    if ($user instanceof User) {
      return $this->users->update($user, [
        'password' => $request->input('password')
      ]);
    } else {
      throw (new NotFoundError())->setContext([
        'id' => [
          __('users.id.not_found')
        ]
      ]);
    }
  }

  /**
   * Request a change to the specified email.
   *
   * @param Illuminate\Http\Request $request
   * @param integer $id
   * @return Illuminate\Http\Response
   */
  public function requestEmailChange(Request $request, $id)
  {
    $user = $this->users->findById($id);

    if ($user instanceof User) {
      if ($this->users->requestEmailChange($user, $request->input('email'))) {
        return response([
          'message' => __('emails.sent')
        ], 202);
      }
    } else {
      throw (new NotFoundError())->setContext([
        'id' => [
          __('users.id.not_found')
        ]
      ]);
    }
  }

  /**
   * Verify the user's email.
   *
   * @param Illuminate\Http\Request $request
   * @param string $email
   * @return Illuminate\Http\Response
   */
  public function verifyEmail(Request $request, $email)
  {
    $user = $this->users->findByEmail($email);

    if ($user instanceof User) {
      return $this->users->verifyEmail($user, $request->query('email_verification_token'));
    } else {
      throw (new NotFoundError())->setContext([
        'email' => [
          __('users.email.not_found')
        ]
      ]);
    }
  }

  /**
   * Resend the user's email verification token.
   *
   * @param string $email
   * @return Illuminate\Http\Response
   */
  public function resendEmailVerificationToken($email)
  {
    $user = $this->users->findByEmail($email);

    if ($user instanceof User) {
      try {
        $this->users->sendEmailVerificationToken($user);

        return response([], 202);
      } catch (InvalidEmailVerificationTokenException $e) {
        throw $e->setContext([
          'email_verification_token' => [
            __('users.invalid_email_verification_token')
          ]
        ]);
      }
    } else {
      throw (new NotFoundError())->setContext([
        'email' => [
          __('users.email.not_found')
        ]
      ]);
    }
  }

  /**
   * Send the user a password reset token.
   *
   * @param string $email
   * @return Illuminate\Http\Response
   */
  public function forgotPassword($email)
  {
    $user = $this->users->findByEmail($email);

    if ($user instanceof User) {
      $this->users->forgotPassword($user);

      return response([
        'message' => __('passwords.sent')
      ], 202);
    } else {
      throw (new NotFoundError())->setContext([
        'email' => [
          __('users.email.not_found')
        ]
      ]);
    }
  }

  /**
   * Reset the user's password using the password reset token.
   *
   * @param Illuminate\Http\Request $request
   * @param string $email
   * @return Illuminate\Http\Response
   */
  public function resetPassword(Request $request, $email)
  {
    $user = $this->users->findByEmail($email);

    if ($user instanceof User) {
      try {
        return $this->users->resetPassword($user, $request->input('password'), $request->query('password_reset_token'));
      } catch (InvalidPasswordResetTokenException $e) {
        throw $e->setContext([
          'password_reset_token' => [
            __('passwords.invalid_password_reset_token')
          ]
        ]);
      } catch (PasswordResetTokenExpiredException $e) {
        throw $e->setContext([
          'password_reset_token' => [
            __('passwords.invalid_password_reset_token')
          ]
        ]);
      }
    } else {
      throw (new NotFoundError())->setContext([
        'email' => [
          __('users.email.not_found')
        ]
      ]);
    }
  }
}
