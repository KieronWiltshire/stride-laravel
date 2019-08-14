<?php

namespace Domain\Role;

use Domain\Role\Contracts\Repositories\RoleRepository;
use Domain\Role\Exceptions\CannotCreateRoleException;
use Domain\Role\Exceptions\CannotUpdateRoleException;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\Exceptions\UnableToSetDefaultRoleException;
use Domain\User\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Support\Exceptions\Pagination\InvalidPaginationException;

class RoleService
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new user service instance.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        RoleRepository $roleRepository
    ) {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Retrieve all of the roles.
     *
     * @return Collection<Role>
     */
    public function all()
    {
        return $this->roleRepository->with(['permissions'])->all();
    }

    /**
     * Create a new role.
     *
     * @param array $attributes
     * @return Role
     *
     * @throws CannotCreateRoleException
     */
    public function create($attributes)
    {
        return $this->roleRepository->create($attributes);
    }

    /**
     * Create a role if the specified search parameters could not find one
     * with the matching criteria.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param array $attributes
     * @return Role
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
    {
        return $this->roleRepository->with(['permissions'])->firstOrCreate($parameter, $search, $regex, $attributes);
    }

    /**
     * Find a role by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true)
    {
        return $this->roleRepository->with(['permissions'])->find($parameter, $search, $regex);
    }

    /**
     * Find a role by identifier.
     *
     * @param string $id
     * @return Role
     */
    public function findById($id)
    {
        return $this->roleRepository->with(['permissions'])->findById($id);
    }

    /**
     * Find a role by name.
     *
     * @param string $name
     * @return Role
     */
    public function findByName($name)
    {
        return $this->roleRepository->with(['permissions'])->findByName($name);
    }

    /**
     * Set the specified role as the default role.
     *
     * @param Role $role
     * @return void
     */
    public function setDefaultRole(Role $role)
    {
        $this->roleRepository->setDefaultRole($role);
    }

    /**
     * Retrieve the default user role.
     *
     * @return Role
     */
    public function getDefaultRole()
    {
        return $this->roleRepository->with(['permissions'])->getDefaultRole();
    }

    /**
     * Update a role.
     *
     * @param Role $role
     * @param array $attributes
     * @return Role
     *
     * @throws CannotUpdateRoleException
     */
    public function update(Role $role, $attributes)
    {
        return $this->update($role, $attributes);
    }

    /**
     * Retrieve an index of the roles.
     *
     * @param integer $limit
     * @param integer $offset
     * @return LengthAwarePaginator
     * @throws InvalidPaginationException
     */
    public function index($limit = null, $offset = 1)
    {
        return $this->roleRepository->with(['permissions'])->paginate($limit, $offset)->all();
    }

    /**
     * Search for roles with the specified search parameters.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param integer $limit
     * @param integer $offset
     * @return LengthAwarePaginator
     * @throws InvalidPaginationException
     */
    public function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
    {
        return $this->roleRepository->with(['permissions'])->paginate($limit, $offset)->find($parameter, $search, $regex);
    }

    /**
     * Add a role to the user.
     *
     * @param User $user
     * @param Role $role
     * @param boolean $persist
     * @return User
     */
    public function addRoleToUser(User $user, Role $role, $persist = true)
    {
        return $this->roleRepository->addRoleToUser($user, $role, $persist);
    }

    /**
     * Add roles to the user.
     *
     * @param User $user
     * @param Collection|array $roles
     * @param boolean $persist
     * @return User
     */
    public function addRolesToUser(User $user, $roles = [], $persist = true)
    {
        return $this->roleRepository->addRolesToUser($user, $roles, $persist);
    }

    /**
     * Remove a role from the user.
     *
     * @param User $user
     * @param Role $role
     * @param boolean $persist
     * @return User
     */
    public function removeRoleFromUser(User $user, Role $role, $persist = true)
    {
        return $this->roleRepository->removeRolesFromUser($user, $role, $persist);
    }

    /**
     * Remove roles from the user.
     *
     * @param User $user
     * @param Collection|array $roles
     * @param boolean $persist
     * @return User
     */
    public function removeRolesFromUser(User $user, $roles = [], $persist = true)
    {
        return $this->roleRepository->removeRolesFromUser($user, $roles, $persist);
    }

    /**
     * Set all of the roles of the specified user.
     *
     * @param User $user
     * @param Collection|array $roles
     * @param boolean $persist
     * @return User
     */
    public function setUserRoles(User $user, $roles = [], $persist = true)
    {
        return $this->roleRepository->setUserRoles($user, $roles, $persist);
    }

    /**
     * Retrieve all of the roles for the specified user.
     *
     * @param User $user
     * @return Collection
     */
    public function getRolesForUser(User $user)
    {
        return $this->roleRepository->getRolesForUser($user);
    }

    /**
     * Determine if the given user has the specified role assigned.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function userHasRole(User $user, Role $role)
    {
        return ($this->getRoles($user)->where('id', $role->id)->count() > 0);
    }

    /**
     * Determine if the given user has the specified roles assigned.
     *
     * @param User $user
     * @param Collection|array $roles
     * @return bool
     */
    public function userHasRoles(User $user, $roles)
    {
        $roleIds = ($roles instanceof Collection ? $roles : collect($roles))
            ->map->only(['id'])
            ->flatten()
            ->all();

        return ($this->getRoles($user)->whereIn('id', $roleIds)->count() > 0);
    }

    /**
     * Retrieve all of the users that are associated with the specified role.
     *
     * @param Role $role
     * @return Collection
     */
    public function getUsersWithRole(Role $role)
    {
        return $this->roleRepository->getUsersWithRole($role);
    }

    /**
     * Retrieve all of the users that are associated with any of the specified roles.
     *
     * @param Collection|array $roles
     * @return Collection
     */
    public function getUsersWithRoles($roles = [])
    {
        return $this->roleRepository->getUsersWithRoles($roles);
    }
}
