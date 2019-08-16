<?php

namespace Domain\Permission\Contracts\Repositories;

use Domain\Permission\Exceptions\CannotCreatePermissionException;
use Domain\Permission\Exceptions\CannotUpdatePermissionException;
use Domain\Permission\Exceptions\PermissionNotFoundException;
use Domain\Role\Role;
use Domain\User\User;
use Illuminate\Database\Eloquent\Collection;
use Support\Contracts\Repositories\AppRepository;
use Domain\Permission\Permission;

interface PermissionRepository extends AppRepository
{

    /**
     * Retrieve all of the permissions.
     *
     * @return Collection
     */
    public function all();

    /**
     * Create a new permission.
     *
     * @param array $attributes
     * @return Permission
     *
     * @throws CannotCreatePermissionException
     */
    public function create($attributes);

    /**
     * Create a permission if the specified search parameters could not find one
     * with the matching criteria.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param array $attributes
     * @return Permission
     *
     * @throws CannotCreatePermissionException
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = []);

    /**
     * Find a permission by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true);

    /**
     * Find a permission by identifier.
     *
     * @param string $id
     * @return Permission
     *
     * @throws PermissionNotFoundException
     */
    public function findById($id);

    /**
     * Find a permission by name.
     *
     * @param string $name
     * @return Permission
     *
     * @throws PermissionNotFoundException
     */
    public function findByName($name);

    /**
     * Update a permission.
     *
     * @param Permission $permission
     * @param array $attributes
     * @return Permission
     *
     * @throws CannotUpdatePermissionException
     */
    public function update(Permission $permission, $attributes);

    /**
     * Add a permission to the specified role.
     *
     * @param Role $role
     * @param Permission $permission
     * @return Role
     */
    public function addPermissionToRole(Role $role, Permission $permission);

    /**
     * Add a permission to the specified user.
     *
     * @param User $user
     * @param Permission $permission
     * @return User
     */
    public function addPermissionToUser(User $user, Permission $permission);

    /**
     * Add multiple permissions to the specified role.
     *
     * @param Role $role
     * @param Collection|array $permissions
     * @return Role
     */
    public function addPermissionsToRole(Role $role, $permissions = []);

    /**
     * Add multiple permissions to the specified user.
     *
     * @param User $user
     * @param Collection|array $permissions
     * @return User
     */
    public function addPermissionsToUser(User $user, $permissions = []);

    /**
     * Remove a permission from the specified role.
     *
     * @param Role $role
     * @param Permission $permission
     * @return Role
     */
    public function removePermissionFromRole(Role $role, Permission $permission);

    /**
     * Remove a permission from the specified user.
     *
     * @param User $user
     * @param Permission $permission
     * @return User
     */
    public function removePermissionFromUser(User $user, Permission $permission);

    /**
     * Remove multiple permissions from the specified role.
     *
     * @param Role $role
     * @param Collection|array $permissions
     * @return Role
     */
    public function removePermissionsFromRole(Role $role, $permissions = []);

    /**
     * Remove multiple permissions from the specified user.
     *
     * @param User $user
     * @param Collection|array $permissions
     * @return User
     */
    public function removePermissionsFromUser(User $user, $permissions = []);

    /**
     * Set all of the permissions of the specified role.
     *
     * @param Role $role
     * @param Collection|array $permissions
     * @return Role
     */
    public function setRolePermissions(Role $role, $permissions = []);

    /**
     * Set all of the permissions of the specified user.
     *
     * @param User $user
     * @param Collection|array $permissions
     * @return User
     */
    public function setUserPermissions(User $user, $permissions = []);

    /**
     * Retrieve all of the permissions for the specified role.
     *
     * @param Role $role
     * @return Collection
     */
    public function getPermissionsForRole(Role $role);

    /**
     * Retrieve all of the permissions for the specified user.
     *
     * @param User $user
     * @return Collection
     */
    public function getPermissionsForUser(User $user);

    /**
     * Retrieve all of the roles that have access to the specified permission.
     *
     * @param Permission $permission
     * @return Collection
     */
    public function getRolesWithPermission(Permission $permission);

    /**
     * Retrieve all of the users that have access to the specified permission.
     *
     * @param Permission $permission
     * @return Collection
     */
    public function getUsersWithPermission(Permission $permission);

    /**
     * Retrieve all of the roles that have access to any of the specified permissions.
     *
     * @param Collection|array $permissions
     * @return Collection
     */
    public function getRolesWithPermissions($permissions = []);

    /**
     * Retrieve all of the users that have access to any of the specified permissions.
     *
     * @param Collection|array $permissions
     * @return Collection
     */
    public function getUsersWithPermissions($permissions = []);
}
