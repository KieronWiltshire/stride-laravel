<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;
use App\Exceptions\Http\BadRequestError;
use App\Exceptions\Http\NotFoundError;
use App\Exceptions\User\PasswordResetTokenExpiredException;
use App\Exceptions\User\InvalidPasswordResetTokenException;

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
   * @return Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $user = $this->users->findByEmail($request->query('email'));

    if ($user instanceof User) {
      return $this->users->update($user, [
        'password' => $request->input('password')
      ]);
    } else {
      throw (new NotFoundError())->setContext([
        'email' => [
          __('passwords.not_found')
        ]
      ]);
    }
  }

  /**
   * Send the user a password reset token.
   *
   * @param Illuminate\Http\Request $request
   * @return Illuminate\Http\Response
   */
  public function forgotPassword(Request $request)
  {
    $user = $this->users->findByEmail($request->query('email'));

    if ($user instanceof User) {
      $this->users->forgotPassword($user);

      return response([], 202);
    } else {
      throw (new NotFoundError())->setContext([
        'email' => [
          __('passwords.not_found')
        ]
      ]);
    }
  }

  /**
   * Reset the user's password using the password reset token.
   *
   * @param Illuminate\Http\Request $request
   * @return Illuminate\Http\Response
   */
  public function resetPassword(Request $request)
  {
    $user = $this->users->findByEmail($request->query('email'));

    if ($user instanceof User) {
      try {
        return $this->users->resetPassword($user, $request->input('password'), $request->query('password_reset_token'));
      } catch (InvalidPasswordResetTokenException $e) {
        throw $e->setContext([
          'password_reset_token' => [
            __('passwords.invalid')
          ]
        ]);
      } catch (PasswordResetTokenExpiredException $e) {
        throw $e->setContext([
          'password_reset_token' => [
            __('passwords.invalid')
          ]
        ]);
      }
    } else {
      throw (new NotFoundError())->setContext([
        'email' => [
          __('passwords.not_found')
        ]
      ]);
    }
  }
}
