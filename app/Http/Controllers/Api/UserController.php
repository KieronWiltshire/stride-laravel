<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Domain\Permission\Exceptions\PermissionAssignedException;
use Domain\Permission\Exceptions\PermissionNotAssignedException;
use Domain\Permission\Exceptions\PermissionNotFoundException;
use Domain\Permission\PermissionService;
use App\Transformers\PermissionTransformer;
use Domain\Role\Exceptions\RoleAssignedException;
use Domain\Role\Exceptions\RoleNotAssignedException;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\RoleService;
use App\Transformers\RoleTransformer;
use Domain\User\Exceptions\CannotCreateUserException;
use Domain\User\Exceptions\CannotUpdateUserException;
use Domain\User\Exceptions\InvalidEmailException;
use Domain\User\Exceptions\InvalidEmailVerificationTokenException;
use Domain\User\Exceptions\InvalidPasswordException;
use Domain\User\Exceptions\InvalidPasswordResetTokenException;
use Domain\User\Exceptions\PasswordResetTokenExpiredException;
use Domain\User\Exceptions\UserNotFoundException;
use App\Transformers\UserTransformer;
use Domain\User\User;
use Domain\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Support\Exceptions\Http\BadRequestError;
use Support\Exceptions\Pagination\InvalidPaginationException;
use Support\Serializers\Fractal\OptionalDataKeySerializer;

class UserController extends Controller
{
  /**
   * @var UserService
   */
  protected $userService;

  /**
   * @var UserTransformer
   */
  protected $userTransformer;

  /**
   * @var RoleService
   */
  protected $roleService;

  /**
   * @var RoleTransformer
   */
  protected $roleTransformer;

  /**
   * @var PermissionService
   */
  protected $permissionService;

  /**
   * @var PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * @var OptionalDataKeySerializer
   */
  protected $noDataKeySerializer;

  /**
   * Create a new user controller instance
   *
   * @param UserService $userService
   * @param UserTransformer $userTransformer
   * @param RoleService $roleService
   * @param RoleTransformer $roleTransformer
   * @param PermissionService $permissionService
   * @param PermissionTransformer $permissionTransformer
   */
  public function __construct(
    UserService $userService,
    UserTransformer $userTransformer,
    RoleService $roleService,
    RoleTransformer $roleTransformer,
    PermissionService $permissionService,
    PermissionTransformer $permissionTransformer
  ) {
    $this->userService = $userService;
    $this->userTransformer = $userTransformer;
    $this->roleService = $roleService;
    $this->roleTransformer = $roleTransformer;
    $this->permissionService = $permissionService;
    $this->permissionTransformer = $permissionTransformer;
    $this->noDataKeySerializer = new OptionalDataKeySerializer(false);
  }

  /**
   * Retrieve an index of users.
   *
   * @return LengthAwarePaginator<\Domain\User\User>
   *
   * @throws InvalidPaginationException
   */
  public function index()
  {
    $users = $this->userService->index(request()->query('limit'), request()->query('offset'))->setPath(route('api.user.index'));

    return fractal($this->includeDefaultRole($users), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
  }

  /**
   * Create a new user.
   *
   * @return User
   *
   * @throws CannotCreateUserException
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
   * @return User
   *
   * @throws UserNotFoundException
   */
  public function getById($id)
  {
    try {
      $user = $this->userService->findById($id);

      return fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @return User
   *
   * @throws UserNotFoundException
   */
  public function getByEmail($email)
  {
    try {
      $user = $this->userService->findByEmail($email);

      return fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @return LengthAwarePaginator<\Domain\User\User>
   *
   * @throws BadRequestError
   * @throws InvalidPaginationException
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

    return fractal($this->includeDefaultRole($users), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
  }

  /**
   * Update a user.
   *
   * @param integer $id
   * @return User
   *
   * @throws UserNotFoundException
   * @throws CannotUpdateUserException
   */
  public function update($id)
  {
    try {
      $user = $this->userService->findById($id);
      $this->authorize('user.update', $user);

      $user = $this->userService->update($user, [
        'password' => request()->input('password')
      ]);

      return fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @return JsonResponse
   *
   * @throws UserNotFoundException
   * @throws InvalidEmailException
   */
  public function requestEmailChange($id)
  {
    try {
      $user = $this->userService->findById($id);
      $this->authorize('user.update', $user);

      $this->userService->requestEmailChange($user, request()->input('email'));

      return response()->json([
        'message' => __('email.email_verification_sent'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions'])
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
   * @return User
   *
   * @throws UserNotFoundException
   * @throws InvalidEmailException
   * @throws InvalidEmailVerificationTokenException
   */
  public function verifyEmail($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $user = $this->userService->verifyEmail($user, request()->query('email_verification_token'));

      return fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @return JsonResponse
   *
   * @throws InvalidEmailVerificationTokenException
   * @throws UserNotFoundException
   */
  public function resendEmailVerificationToken($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $this->userService->sendEmailVerificationToken($user);

      return response()->json([
        'message' => __('email.email_verification_resent'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions'])
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
   * @return JsonResponse
   *
   * @throws UserNotFoundException
   */
  public function forgotPassword($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $this->userService->forgotPassword($user);

      return response([
        'message' => __('passwords.sent'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions'])
      ], 202);
    } catch (MenuNotFoundException $e) {
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
   * @return User
   *
   * @throws UserNotFoundException
   * @throws PasswordResetTokenExpiredException
   * @throws InvalidPasswordResetTokenException
   * @throws InvalidPasswordException
   */
  public function resetPassword($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $user = $this->userService->resetPassword($user, request()->input('password'), request()->query('password_reset_token'));

      return fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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

  /**
   * Add the specified role to the specified user.
   *
   * @param $id
   * @param $roleId
   * @return JsonResponse
   */
  public function assignRole($id, $roleId)
  {
    try {
      $user = $this->userService->findById($id);
      $role = $this->roleService->findById($roleId);

      $this->authorize('user.assign-role', $user);
      $this->authorize('role.assign', $role);

      if ($this->roleService->userHasRole($user, $role)) {
        throw new RoleAssignedException();
      }

      $this->roleService->addRoleToUser($user, $role);

      return response([
        'message' => __('user.role.assigned'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer)->parseIncludes(['roles', 'permissions'])
      ], 200);
    } catch (RoleNotFoundException $e) {
      throw $e->setContext([
        'roleId' => [
          __('role.id.not_found')
        ]
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
   * Add the specified roles to the specified user.
   *
   * @param $id
   * @return JsonResponse
   */
  public function assignRoles($id)
  {
    try {
      $user = $this->userService->findById($id);
      $roles = $this->roleService->find('id', request()->input('roleIds'));

      if ($roles->count() != (is_array($roles) ? count(request()->input('roleIds')) : 1)) {
        throw new RoleNotFoundException();
      }

      $this->authorize('user.assign-role', $user);

      foreach ($roles as $role) {
        $this->authorize('role.assign', $role);
      }

      if ($this->roleService->userHasRoles($user, $roles)) {
        throw new RoleAssignedException();
      }

      $this->roleService->addRolesToUser($user, $roles);

      return response([
        'message' => __('user.role.assigned'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions']),
      ], 200);
    } catch (RoleNotFoundException $e) {
      throw $e->setContext([
        'roleId' => [
          __('role.id.not_found')
        ]
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
   * Remove the specified role from the specified user.
   *
   * @param $id
   * @param $roleId
   * @return JsonResponse
   */
  public function denyRole($id, $roleId)
  {
    try {
      $user = $this->userService->findById($id);
      $role = $this->roleService->findById($roleId);

      $this->authorize('user.deny-role', $user);
      $this->authorize('role.deny', $role);

      if (!$this->roleService->userHasRole($user, $role)) {
        throw new RoleNotAssignedException();
      }

      $this->roleService->removeRoleFromUser($user, $role);

      return response([
        'message' => __('user.role.denied'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
      ], 200);
    } catch (RoleNotFoundException $e) {
      throw $e->setContext([
        'roleId' => [
          __('role.id.not_found')
        ]
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
   * Remove the specified roles from the specified user.
   *
   * @param $id
   * @return JsonResponse
   */
  public function denyRoles($id)
  {
    try {
      $user = $this->userService->findById($id);
      $roles = $this->roleService->find('id', request()->input('roleIds'));

      if ($roles->count() != (is_array($roles) ? count(request()->input('roleIds')) : 1)) {
        throw new RoleNotFoundException();
      }

      $this->authorize('user.deny-role', $user);

      foreach ($roles as $role) {
        $this->authorize('role.deny', $role);
      }

      if (!$this->roleService->userHasRoles($user, $roles)) {
        throw new RoleNotAssignedException();
      }

      $this->roleService->removeRolesFromUser($user, $roles);

      return response([
        'message' => __('user.role.assigned'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
      ], 200);
    } catch (RoleNotFoundException $e) {
      throw $e->setContext([
        'roleId' => [
          __('role.id.not_found')
        ]
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
   * Add the specified permission to the specified user.
   *
   * @param $id
   * @param $permissionId
   * @return JsonResponse
   */
  public function assignPermission($id, $permissionId)
  {
    try {
      $user = $this->userService->findById($id);
      $permission = $this->permissionService->findById($permissionId);

      $this->authorize('user.assign-permission', $user);
      $this->authorize('permission.assign', $permission);

      if ($this->permissionService->userHasPermission($user, $permission)) {
        throw new PermissionAssignedException();
      }

      $this->permissionService->addPermissionToUser($user, $permission);

      return response([
        'message' => __('user.permission.assigned'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
      ], 200);
    } catch (PermissionNotFoundException $e) {
      throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
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
   * Add the specified permissions to the specified user.
   *
   * @param $id
   * @return JsonResponse
   */
  public function assignPermissions($id)
  {
    try {
      $user = $this->userService->findById($id);
      $permissions = $this->permissionService->find('id', request()->input('permissionIds'));

      if ($permissions->count() != (is_array($permissions) ? count(request()->input('permissionIds')) : 1)) {
        throw new PermissionNotFoundException();
      }

      $this->authorize('user.assign-permission', $user);

      foreach ($permissions as $permission) {
        $this->authorize('permission.assign', $permission);
      }

      if ($this->permissionService->userHasPermissions($user, $permissions)) {
        throw new PermissionAssignedException();
      }

      $this->permissionService->addPermissionToUser($user, $permissions);

      return response([
        'message' => __('user.permission.assigned'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
      ], 200);
    } catch (PermissionNotFoundException $e) {
      throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
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
   * Remove the specified permission from the specified user.
   *
   * @param $id
   * @param $permissionId
   * @return JsonResponse
   */
  public function denyPermission($id, $permissionId)
  {
    try {
      $user = $this->userService->findById($id);
      $permission = $this->permissionService->findById($permissionId);

      $this->authorize('user.deny-permission', $user);
      $this->authorize('permission.deny', $permission);

      if (!$this->permissionService->userHasPermission($user, $permission)) {
        throw new PermissionNotAssignedException();
      }

      $this->permissionService->removePermissionFromUser($user, $permission);

      return response([
        'message' => __('user.permission.denied'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
      ], 200);
    } catch (PermissionNotFoundException $e) {
      throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
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
   * Remove the specified permissions from the specified user.
   *
   * @param $id
   * @return JsonResponse
   */
  public function denyPermissions($id)
  {
    try {
      $user = $this->userService->findById($id);
      $permissions = $this->permissionService->find('id', request()->input('permissionIds'));

      if ($permissions->count() != (is_array($permissions) ? count(request()->input('permissionIds')) : 1)) {
        throw new PermissionNotFoundException();
      }

      $this->authorize('user.deny-permission', $user);

      foreach ($permissions as $permission) {
        $this->authorize('permission.deny', $permission);
      }

      if (!$this->permissionService->userHasPermissions($user, $permissions)) {
        throw new PermissionNotAssignedException();
      }

      $this->permissionService->removePermissionsFromUser($user, $permissions);

      return response([
        'message' => __('user.permission.denied'),
        'data' => fractal($this->includeDefaultRole($user), $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
      ], 200);
    } catch (PermissionNotFoundException $e) {
      throw $e->setContext([
        'permissionId' => [
          __('permission.id.not_found')
        ]
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
   * Include the default role with the user response
   * if the specified user has no roles.
   *
   * @param User $user
   * @return User
   */
  protected function includeDefaultRole(&$user) {
    try {
      $defaultRole = $this->roleService->getDefaultRole();

      if (is_iterable($user)) {
        foreach ($user as $u) {
          $this->includeDefaultRole($u);
        }
      } else {
        if ($defaultRole) {
          if ($this->roleService->getRolesFromUser($user)->count() <= 0) {
            $this->roleService->addRoleToUser($user, $this->roleService->getDefaultRole(), false);
          }
        }
      }
    } catch (RoleNotFoundException $e) {
      // Do nothing if there is no default role is configured
    }

    return $user;
  }
}
