<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Domain\Permission\Exceptions\PermissionAssignedException;
use Domain\Permission\Exceptions\PermissionNotAssignedException;
use Domain\Permission\Exceptions\PermissionNotFoundException;
use Domain\Permission\PermissionService;
use Domain\Permission\Transformers\PermissionTransformer;
use Domain\Role\Exceptions\RoleAssignedException;
use Domain\Role\Exceptions\RoleNotAssignedException;
use Domain\Role\Exceptions\RoleNotFoundException;
use Domain\Role\RoleService;
use Domain\Role\Transformers\RoleTransformer;
use Domain\User\Exceptions\InvalidEmailVerificationTokenException;
use Domain\User\Exceptions\InvalidPasswordResetTokenException;
use Domain\User\Exceptions\PasswordResetTokenExpiredException;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Transformers\UserTransformer;
use Domain\User\UserService;
use Infrastructure\Exceptions\Http\BadRequestError;
use Infrastructure\Serializers\Fractal\OptionalDataKeySerializer;

class UserController extends Controller
{
  /**
   * @var \Domain\User\UserService
   */
  protected $userService;

  /**
   * @var \Domain\User\Transformers\UserTransformer
   */
  protected $userTransformer;

  /**
   * @var \Domain\Role\RoleService
   */
  protected $roleService;

  /**
   * @var \Domain\Role\Transformers\RoleTransformer
   */
  protected $roleTransformer;

  /**
   * @var \Domain\Permission\PermissionService
   */
  protected $permissionService;

  /**
   * @var \Domain\Permission\Transformers\PermissionTransformer
   */
  protected $permissionTransformer;

  /**
   * @var \Infrastructure\Serializers\Fractal\OptionalDataKeySerializer
   */
  protected $noDataKeySerializer;

  /**
   * Create a new user controller instance
   *
   * @param \Domain\User\UserService $userService
   * @param \Domain\User\Transformers\UserTransformer $userTransformer
   * @param \Domain\Role\RoleService $roleService
   * @param \Domain\Role\Transformers\RoleTransformer $roleTransformer
   * @param \Domain\Permission\PermissionService $permissionService
   * @param \Domain\Permission\Transformers\PermissionTransformer $permissionTransformer
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\User\User>
   *
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
   */
  public function index()
  {
    $users = $this->userService->index(request()->query('limit'), request()->query('offset'))->setPath(route('api.user.index'));

    return fractal($users, $this->userTransformer)->parseIncludes(['roles', 'permissions']);
  }

  /**
   * Create a new user.
   *
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\CannotCreateUserException
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
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function getById($id)
  {
    try {
      return fractal($this->userService->findById($id), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function getByEmail($email)
  {
    try {
      return fractal($this->userService->findByEmail($email), $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @return \Illuminate\Pagination\LengthAwarePaginator<\Domain\User\User>
   *
   * @throws \Infrastructure\Exceptions\Http\BadRequestError
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
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

    return fractal($users, $this->userTransformer)->parseIncludes(['roles', 'permissions']);
  }

  /**
   * Update a user.
   *
   * @param integer $id
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\CannotUpdateUserException
   */
  public function update($id)
  {
    try {
      $user = $this->userService->findById($id);
      $this->authorize('user.update', $user);

      $user = $this->userService->update($user, [
        'password' => request()->input('password')
      ]);

      return fractal($user, $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\InvalidEmailException
   */
  public function requestEmailChange($id)
  {
    try {
      $user = $this->userService->findById($id);
      $this->authorize('user.update', $user);

      $this->userService->requestEmailChange($user, request()->input('email'));

      return response()->json([
        'message' => __('email.email_verification_sent')
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
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\InvalidEmailException
   * @throws \Domain\User\Exceptions\InvalidEmailVerificationTokenException
   */
  public function verifyEmail($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $user = $this->userService->verifyEmail($user, request()->query('email_verification_token'));

      return fractal($user, $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @throws \Domain\User\Exceptions\InvalidEmailVerificationTokenException
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function resendEmailVerificationToken($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $this->userService->sendEmailVerificationToken($user);

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
   * @throws \Domain\User\Exceptions\UserNotFoundException
   */
  public function forgotPassword($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $this->userService->forgotPassword($user);

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
   * @return \Domain\User\User
   *
   * @throws \Domain\User\Exceptions\UserNotFoundException
   * @throws \Domain\User\Exceptions\PasswordResetTokenExpiredException
   * @throws \Domain\User\Exceptions\InvalidPasswordResetTokenException
   * @throws \Domain\User\Exceptions\InvalidPasswordException
   */
  public function resetPassword($email)
  {
    try {
      $user = $this->userService->findByEmail($email);
      $user = $this->userService->resetPassword($user, request()->input('password'), request()->query('password_reset_token'));

      return fractal($user, $this->userTransformer)->parseIncludes(['roles', 'permissions']);
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function assignRole($id)
  {
    try {
      $user = $this->userService->findById($id);
      $role = $this->roleService->findById(request()->input('roleId'));

      $this->authorize('user.assign-role', $user);
      $this->authorize('role.assign', $role);

      if ($this->userService->hasRole($user, $role)) {
        throw new RoleAssignedException();
      }

      $this->userService->addRole($user, $role);

      return response([
        'message' => __('user.role.assigned'),
        'data' => fractal($user, $this->userTransformer)->parseIncludes(['roles', 'permissions'])
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function assignRoles($id)
  {
    try {
      $user = $this->userService->findById($id);
      $roles = $this->roleService->find('id', request()->input('roleIds'));

      if ($roles->count() != count(request()->input('roleIds'))) {
        throw new RoleNotFoundException();
      }

      $this->authorize('user.assign-role', $user);

      foreach ($roles as $role) {
        $this->authorize('role.assign', $role);
      }

      if ($this->userService->hasRoles($user, $roles)) {
        throw new RoleAssignedException();
      }

      $this->userService->addRoles($user, $roles);

      return response([
        'message' => __('user.role.assigned'),
        'data' => fractal($user, $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions']),
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function denyRole($id)
  {
    try {
      $user = $this->userService->findById($id);
      $role = $this->roleService->findById(request()->input('roleId'));

      $this->authorize('user.deny-role', $user);
      $this->authorize('role.deny', $role);

      if (!$this->userService->hasRole($user, $role)) {
        throw new RoleNotAssignedException();
      }

      $this->userService->removeRole($user, $role);

      return response([
        'message' => __('user.role.denied'),
        'data' => fractal($user, $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function denyRoles($id)
  {
    try {
      $user = $this->userService->findById($id);
      $roles = $this->roleService->find('id', request()->input('roleIds'));

      if ($roles->count() != count(request()->input('roleIds'))) {
        throw new RoleNotFoundException();
      }

      $this->authorize('user.deny-role', $user);

      foreach ($roles as $role) {
        $this->authorize('role.deny', $role);
      }

      if (!$this->userService->hasRoles($user, $roles)) {
        throw new RoleNotAssignedException();
      }

      $this->userService->removeRoles($user, $roles);

      return response([
        'message' => __('user.role.assigned'),
        'data' => fractal($user, $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function assignPermission($id)
  {
    try {
      $user = $this->userService->findById($id);
      $permission = $this->permissionService->findById(request()->input('permissionId'));

      $this->authorize('user.assign-permission', $user);
      $this->authorize('permission.assign', $permission);

      if ($this->userService->hasPermission($user, $permission)) {
        throw new PermissionAssignedException();
      }

      $this->userService->addPermission($user, $permission);

      return response([
        'message' => __('user.permission.assigned'),
        'data' => fractal($user, $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function assignPermissions($id)
  {
    try {
      $user = $this->userService->findById($id);
      $permissions = $this->permissionService->find('id', request()->input('permissionIds'));

      if ($permissions->count() != count(request()->input('permissionIds'))) {
        throw new PermissionNotFoundException();
      }

      $this->authorize('user.assign-permission', $user);

      foreach ($permissions as $permission) {
        $this->authorize('permission.assign', $permission);
      }

      if ($this->userService->hasPermissions($user, $permissions)) {
        throw new PermissionAssignedException();
      }

      $this->userService->addPermission($user, $permissions);

      return response([
        'message' => __('user.permission.assigned'),
        'data' => fractal($user, $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function denyPermission($id)
  {
    try {
      $user = $this->userService->findById($id);
      $permission = $this->permissionService->findById(request()->input('permissionId'));

      $this->authorize('user.deny-permission', $user);
      $this->authorize('permission.deny', $permission);

      if (!$this->userService->hasPermission($user, $permission)) {
        throw new PermissionNotAssignedException();
      }

      $this->userService->removePermission($user, $permission);

      return response([
        'message' => __('user.permission.denied'),
        'data' => fractal($user, $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
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
   * @return \Illuminate\Http\JsonResponse
   */
  public function denyPermissions($id)
  {
    try {
      $user = $this->userService->findById($id);
      $permissions = $this->permissionService->find('id', request()->input('permissionIds'));

      if ($permissions->count() != count(request()->input('permissionIds'))) {
        throw new PermissionNotFoundException();
      }

      $this->authorize('user.deny-permission', $user);

      foreach ($permissions as $permission) {
        $this->authorize('permission.deny', $permission);
      }

      if (!$this->userService->hasPermission($user, $permissions)) {
        throw new PermissionNotAssignedException();
      }

      $this->userService->removePermissions($user, $permissions);

      return response([
        'message' => __('user.permission.denied'),
        'data' => fractal($user, $this->userTransformer, $this->noDataKeySerializer)->parseIncludes(['roles', 'permissions'])
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
}
