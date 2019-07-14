<?php

namespace Domain\User;

use Domain\Permission\Permission;
use Domain\Role\Role;
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

  /**
   * Add a role to the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return \Domain\User\User
   */
  public function addRole(User $user, Role $role)
  {
    return $this->addRoles($user, [
      $role
    ]);
  }

  /**
   * Add roles to the user.
   *
   * @param \Domain\User\User $user
   * @param array $roles
   * @return \Domain\User\User
   */
  public function addRoles(User $user, array $roles = [])
  {
    return $user->attachRoles($roles);
  }

  /**
   * Remove a role from the user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Role\Role $role
   * @return \Domain\User\User
   */
  public function removeRole(User $user, Role $role)
  {
    return $this->removeRoles($user, [
      $role
    ]);
  }

  /**
   * Remove roles from the user.
   *
   * @param \Domain\User\User $user
   * @param array $roles
   * @return \Domain\User\User
   */
  public function removeRoles(User $user, array $roles = [])
  {
    return $user->detachRoles($roles);
  }

  /**
   * Set all of the roles of the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $roles
   * @return \Domain\User\User
   */
  public function setRoles(User $user, array $roles = [])
  {
    return $user->syncRoles($roles);
  }

  /**
   * Retrieve all of the roles for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Role\Role>
   */
  function getRoles(User $user)
  {
    return $user->roles;
  }

  /**
   * Add a permission to the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  public function addPermission(User $user, Permission $permission)
  {
    return $this->addPermissions($user, [
      $permission
    ]);
  }

  /**
   * Add multiple permissions to the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $permissions
   * @return \Domain\User\User
   */
  public function addPermissions(User $user, array $permissions = [])
  {
    return $user->attachPermissions($permissions);
  }

  /**
   * Remove a permission from the specified user.
   *
   * @param \Domain\User\User $user
   * @param \Domain\Permission\Permission $permission
   * @return \Domain\User\User
   */
  public function removePermission(User $user, Permission $permission)
  {
    return $this->removePermissions($user, [
      $permission
    ]);
  }

  /**
   * Remove multiple permissions from the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $permissions
   * @return \Domain\User\User
   */
  public function removePermissions(User $user, array $permissions = [])
  {
    return $user->detachPermissions($permissions);
  }

  /**
   * Set all of the permissions of the specified user.
   *
   * @param \Domain\User\User $user
   * @param array $permissions
   * @return \Domain\User\User
   */
  public function setPermissions(User $user, array $permissions = [])
  {
    return $user->syncPermissions($permissions);
  }

  /**
   * Retrieve all of the permissions for the specified user.
   *
   * @param \Domain\User\User $user
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\Permission\Permission>
   */
  function getPermissions(User $user)
  {
    return $user->permissions;
  }

  /**
   * Retrieve all of the users that are associated with the specified role.
   *
   * @param \Domain\Role\Role $role
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRole(Role $role)
  {
    return $this->getUsersWithRoles([$role]);
  }

  /**
   * Retrieve all of the users that are associated with any of the specified roles.
   *
   * @param array $roles
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  public function getUsersWithRoles(array $roles = [])
  {
    $query = User::query();

    foreach ($roles as $index => $role) {
      if ($index <= 0) {
        $query->whereRoleIs($role->name);
      } else {
        $query->orWhereRoleIs($role->name);
      }
    }

    return $this->execute($query);
  }

  /**
   * Retrieve all of the users that have access to the specified permission.
   *
   * @param \Domain\Permission\Permission $permission
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithPermission(Permission $permission)
  {
    return $this->getUsersWithPermissions([$permission]);
  }

  /**
   * Retrieve all of the users that have access to any of the specified permissions.
   *
   * @param array $permissions
   * @return \Illuminate\Database\Eloquent\Collection<\Domain\User\User>
   */
  function getUsersWithPermissions(array $permissions = [])
  {
    $query = User::query();

    foreach ($permissions as $index => $permission) {
      if ($index <= 0) {
        $query->wherePermissionIs($permission->name);
      } else {
        $query->orWherePermissionIs($permission->name);
      }
    }

    return $this->execute($query);
  }
}
