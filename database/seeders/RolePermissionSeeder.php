<?php

namespace Database\Seeders;

use App\Enums\Permission as EnumsPermission;
use App\Enums\Role as EnumsRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertPermissions();

        $this->createRoleSuperadmin();

        $this->createRole(EnumsRole::MANAGER_COMPANY->value);
    }

    /**
     * Insert all permissions needed for this app.
     */
    private function insertPermissions(): void
    {
        $permissions = array_map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, EnumsPermission::values());

        Permission::insert($permissions);
    }

    /**
     * Create role by given name and guard web.
     */
    private function createRole(string $name): Role
    {
        /** @var Role */
        $role = Role::create(['name' => $name]);

        return $role;
    }

    /**
     * Create role Superadmin and assign all permissions.
     */
    private function createRoleSuperadmin(): void
    {
        $role = $this->createRole(EnumsRole::SUPERADMIN->value);
        $permissions = Permission::all();
        $role->givePermissionTo($permissions);
    }
}
