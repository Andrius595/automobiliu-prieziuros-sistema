<?php

namespace Database\Seeders;

use App\Config\PermissionsConfig;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemAdmin = Role::firstOrCreate(['name' => PermissionsConfig::SYSTEM_ADMIN_ROLE]);
        $client = Role::firstOrCreate(['name' => PermissionsConfig::CLIENT_ROLE]);
        $serviceEmployee = Role::firstOrCreate(['name' => PermissionsConfig::SERVICE_EMPLOYEE_ROLE]);
        Role::firstOrCreate(['name' => PermissionsConfig::SERVICE_ADMIN_ROLE]);

        $permissions = PermissionsConfig::SYSTEM_ADMIN_PERMISSIONS;
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $systemAdmin->syncPermissions($permissions);

        $permissions = PermissionsConfig::CLIENT_PERMISSIONS;
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $client->syncPermissions($permissions);

        $permissions = PermissionsConfig::SERVICE_EMPLOYEE_PERMISSIONS;
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $serviceEmployee->syncPermissions($permissions);
    }
}
