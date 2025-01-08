<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $userRole = Role::firstOrCreate(['name' => strtolower(RoleEnum::USER->value)]);

        $adminRole = Role::firstOrCreate(['name' => strtolower(RoleEnum::ADMIN->value)]);

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
