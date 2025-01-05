<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        /* Permission::create(['name' => 'edit articles']);
         Permission::create(['name' => 'delete articles']);
         Permission::create(['name' => 'publish articles']);
         Permission::create(['name' => 'unpublish articles']);*/

        // create roles and assign existing permissions
        $userRole = Role::firstOrCreate(['name' => strtolower(RoleEnum::USER->value)]);
        //  $role1->givePermissionTo('edit articles');
        //  $role1->givePermissionTo('delete articles');

        $adminRole = Role::firstOrCreate(['name' => strtolower(RoleEnum::ADMIN->value)]);

        //  $role2->givePermissionTo('publish articles');
        //  $role2->givePermissionTo('unpublish articles');

        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Example User',
            'email' => 'tester@example.com',
            'password' => Hash::make('123'),
        ]);

        $user->assignRole($userRole);

        $admin = \App\Models\User::factory()->create([
            'name' => 'Example Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'),
        ]);
        $admin->assignRole($adminRole);
    }
}
