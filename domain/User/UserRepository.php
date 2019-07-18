<?php

namespace Domain\User;

use Infrastructure\Repositories\AppRepository;
use Domain\User\Contracts\Repositories\UserRepository as UserRepositoryInterface;
use Domain\User\Events\UserCreatedEvent;
use Domain\User\Events\UserUpdatedEvent;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Validators\UserCreateValidator;
use Domain\User\Validators\UserUpdateValidator;
use Exception;

class UserRepository extends AppRepository implements UserRepositoryInterface
{
  /**
   * @var \Domain\User\Validators\UserCreateValidator
   */
  protected $userCreateValidator;

  /**
   * @var \Domain\User\Validators\UserUpdateValidator
   */
  protected $userUpdateValidator;

  /**
   * Create a new user repository instance.
   *
   * @param \Domain\User\Validators\UserCreateValidator $userCreateValidator
   * @param \Domain\User\Validators\UserUpdateValidator $userUpdateValidator
   */
  public function __construct(
    UserCreateValidator $userCreateValidator,
    UserUpdateValidator $userUpdateValidator
  ) {
    $this->userCreateValidator = $userCreateValidator;
    $this->userUpdateValidator = $userUpdateValidator;
  }

  /**
   * Retrieve all of the users.
   *
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function all()
  {
    return $this->execute(User::query());
  }

  /**
   * Create a new user.
   *
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
   */
  public function create($attributes)
  {
    $this->userCreateValidator->validate($attributes);

    if ($user = User::create($attributes)) {
      event(new UserCreatedEvent($user));

      return $user;
    }

    throw new Exception();
  }

  /**
   * Create a user if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
   */
  public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    $query = User::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    $user = $this->execute($query, true);

    return ($user) ? $user : $this->create($attributes);
  }

  /**
   * Find a user by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function find($parameter, $search, $regex = true)
  {
    $query = User::query();

    if (is_array($parameter)) {
      $query->whereIn($parameter, $search);
    } else {
      if ($regex) {
        $query->where($parameter, 'REGEXP', $search);
      } else {
        $query->where($parameter, $search);
      }
    }

    return $this->execute($query);
  }

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function findById($id)
  {
    $user = $this->execute(User::where('id', $id), true);

    if (!$user) {
      throw new UserNotFoundException();
    }

    return $user;
  }

  /**
   * Find a user by email.
   *
   * @param string $email
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function findByEmail($email)
  {
    $user = $this->execute(User::where('email', $email), true);

    if (!$user) {
      throw new UserNotFoundException();
    }

    return $user;
  }

  /**
   * Update a user.
   *
   * @param \Domain\User\User $user
   * @param array $attributes
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotUpdateUserException
   */
  public function update(User $user, $attributes)
  {
    $this->userUpdateValidator->validate($attributes);

    foreach ($attributes as $attr => $value) {
      $user->$attr = $value;
    }

    if ($user->save()) {
      event(new UserUpdatedEvent($user, $attributes));

      return $user;
    }
  }
}
