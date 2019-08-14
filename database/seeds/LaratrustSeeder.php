<?php

use Domain\Permission\PermissionService;
use Domain\Role\RoleService;
use Domain\User\UserService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaratrustSeeder extends Seeder
{
    /**
     * @var \Domain\User\UserService
     */
    private $userService;

    /**
     * @var \Domain\Role\RoleService
     */
    private $roleService;

    /**
     * @var \Domain\Permission\PermissionService
     */
    private $permissionService;

    /**
     * Create a new laratrust seeder instance
     *
     * @param \Domain\User\UserService $userService
     * @param \Domain\Role\RoleService $roleService
     * @param \Domain\Permission\PermissionService $permissionService
     */
    public function __construct(
      UserService $userService,
      RoleService $roleService,
      PermissionService $permissionService
  ) {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
//    $this->command->info('Truncating User, Role and Permission tables');
//    $this->truncateLaratrustTables(); // Disabled this as when new permissions are added, the seeder would truncate the users table

        $config = config('laratrust_seeder.role_structure');
        $userPermission = config('laratrust_seeder.user_structure');
        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        $appName = explode('.', strtolower(config('app.name', 'app')));
        $appAddress = $appName[0] . '.' . (isset($appName[1]) ? $appName[1] : 'com');

        foreach ($config as $key => $modules) {
            $permissions = [];

            // Create a new role
            $role = $this->roleService->firstOrCreate('name', $key, false, [
        'name' => $key,
        'display_name' => ucwords(str_replace('_', ' ', $key)),
        'description' => ucwords(str_replace('_', ' ', $key))
      ]);

            $this->command->info('Creating Role ' . strtoupper($key));

            // Reading role permission modules
            foreach ($modules as $module => $value) {
                foreach (explode(',', $value) as $p => $perm) {
                    $permissionValue = $mapPermission->get($perm);

                    $permissionName = $module . '.' . $permissionValue;

                    $permissions[] = $this->permissionService->firstOrCreate('name', $permissionName, false, [
            'name' => $permissionName,
            'display_name' => ($permissionValue === '*') ? ('All ' . $module) : strtolower($permissionName),
            'description' => strtolower($permissionName),
          ])->id;

                    $this->command->info('Creating Permission to ' . $permissionValue . ' for ' . $module);
                }
            }

            // Attach all permissions to the role
            $this->permissionService->setRolePermissions($role, $permissions);

            $this->command->info("Creating '{$key}' user");

            $userEmail = $key . '@' . $appAddress;

            // Create default user for each role
            $user = $this->userService->firstOrCreate('email', $userEmail, false, [
        'email' => $userEmail,
        'password' => 'password'
      ]);

            $this->roleService->setUserRoles($user, [$role]);
        }

        // Creating user with permissions
        if (!empty($userPermission)) {
            foreach ($userPermission as $key => $modules) {
                $permissions = [];

                $userEmail = $key . '@' . $appAddress;

                // Create default user for each permission set
                $user = $this->userService->firstOrCreate('email', $userEmail, false, [
          'email' => $userEmail,
          'password' => 'password',
        ]);

                foreach ($modules as $module => $value) {
                    foreach (explode(',', $value) as $p => $perm) {
                        $permissionValue = $mapPermission->get($perm);

                        $permissionName = $module . '.' . $permissionValue;

                        $permissions[] = $this->permissionService->firstOrCreate('name', $permissionName, false, [
              'name' => $permissionName,
              'display_name' => ($permissionValue === '*') ? ('All ' . $module . ' permissions') : strtolower($permissionName),
              'description' => strtolower($permissionName),
            ])->id;

                        $this->command->info('Creating Permission to ' . $permissionValue . ' for ' . $module);
                    }
                }

                // Attach all permissions to the user
                $this->permissionService->setUserPermissions($user, $permissions);
            }
        }
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return    void
     */
    public function truncateLaratrustTables()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('permission_role')->truncate();
        DB::table('permission_user')->truncate();
        DB::table('role_user')->truncate();
        \Domain\User\User::truncate();
        \Domain\Role\Role::truncate();
        \Domain\Permission\Permission::truncate();
        Schema::enableForeignKeyConstraints();
    }
}
