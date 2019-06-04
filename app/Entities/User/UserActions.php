<?php

namespace App\Entities\User;

use App\Exceptions\User\CannotCreateUserException;
use App\Exceptions\User\CannotUpdateUserException;
use App\Exceptions\User\InvalidEmailException;
use App\Exceptions\User\InvalidPasswordException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

trait UserActions
{
  /**
   * @var string
   */
  private $emailRules = 'unique:users|email';

  /**
   * @var string
   */
  private $passwordRules = 'min:6';

  /**
   * Validate the specified parameters for creating a user.
   *
   * @param Illuminate\Contracts\Validation\Factory $validationFactory
   * @param array $attributes
   *
   * @throws \App\Exceptions\User\CannotCreateUserException
   */
  protected function validateUserCreateParameters(ValidationFactory $validationFactory, $attributes)
  {
    $validator = $validationFactory->make($attributes, [
      'email' => 'required|' . $this->emailRules,
      'password' => 'required|' . $this->passwordRules,
    ]);

    if ($validator->fails()) {
      throw (new CannotCreateUserException())->setContext($validator->errors()->toArray());
    }
  }

  /**
   * Validate the specified parameters for updating a user.
   *
   * @param Illuminate\Contracts\Validation\Factory $validationFactory
   * @param array $attributes
   *
   * @throws \App\Exceptions\User\CannotUpdateUserException
   */
  protected function validateUserUpdateParameters(ValidationFactory $validationFactory, $attributes)
  {
    $validator = $validationFactory->make($attributes, [
      'email' => $this->emailRules,
      'password' => $this->passwordRules,
    ]);

    if ($validator->fails()) {
      throw (new CannotUpdateUserException())->setContext($validator->errors()->toArray());
    }
  }

  /**
   * Validate the user email parameter.
   *
   * @param Illuminate\Contracts\Validation\Factory $validationFactory
   * @param string $email
   *
   * @throws \App\Exceptions\User\InvalidEmailException
   */
  protected function validateUserEmailParameter(ValidationFactory $validationFactory, $email)
  {
    $validator = $validationFactory->make([
      'email' => $email
    ], [
      'email' => 'required|' . $this->emailRules,
    ]);

    if ($validator->fails()) {
      throw (new InvalidEmailException())->setContext($validator->errors()->toArray());
    }
  }

  /**
   * Validate the user password parameter.
   *
   * @param Illuminate\Contracts\Validation\Factory $validationFactory
   * @param string $password
   *
   * @throws \App\Exceptions\User\InvalidPasswordException
   */
  protected function validateUserPasswordParameter(ValidationFactory $validationFactory, $password) {
    $validator = $validationFactory->make([
      'password' => $password
    ], [
      'password' => 'required|' . $this->passwordRules,
    ]);

    if ($validator->fails()) {
      throw (new InvalidPasswordException())->setContext($validator->errors()->toArray());
    }
  }
}