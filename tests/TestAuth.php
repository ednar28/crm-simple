<?php

namespace Tests;

use App\Enums\Permission as EnumsPermission;
use App\Enums\Role as EnumsRole;
use App\Models\User;
use Spatie\Permission\Models\Role;

trait TestAuth
{
    /**
     * Currently authenticated user.
     */
    protected User $user;

    /**
     * Set user and authenticate them.
     */
    public function authenticate(User $user): void
    {
        $this->user = $user;
        $this->actingAs($user);
    }

    /**
     * Seed and authenticate a user with role superadmin.
     */
    public function actingAsSuperadmin(): void
    {
        $this->authenticate($this->createSuperadmin());
    }

    /**
     * Seed and authenticate a user with role admin.
     * Can also add optional permission as well.
     */
    public function actingAsAdmin(?EnumsPermission $permission = null): void
    {
        $this->authenticate($this->createAdmin($permission));
    }

    /**
     * Seed a user with role superadmin.
     */
    public function createSuperadmin(): User
    {
        return User::factory()->role(EnumsRole::SUPERADMIN->value)->create();
    }

    /**
     * Seed a user with role Admin. Can also add optional permission when
     * creating the user. It will be created if it doesn't exist.
     */
    public function createAdmin(?EnumsPermission $permission = null): User
    {
        $role = fake()->unique()->word();

        $user = User::factory()->role($role)->create();

        $role = Role::findByName($role);
        $role->givePermissionTo(EnumsPermission::ADMIN_APP->value);
        if ($permission) {
            $role->givePermissionTo($permission->value);
        }

        return $user;
    }

    /**
     * Assert user for each role can / can not access the route.
     * Example:
     * $this->assertUserPermission(fn () => $this->jsonGet())
     *     ->allow($this->createSuperadmin())
     *     ->allow($this->createAdmin(Permission::ADMIN_APP))
     *     ->forbid($this->createAdmin());.
     *
     * @param  \Closure(): \Illuminate\Testing\TestResponse  $do
     */
    public function assertUserPermission(\Closure $do)
    {
        $authenticate = fn (User $user) => $this->authenticate($user);

        return new AssertAuthPermission($authenticate, $do);
    }
}
