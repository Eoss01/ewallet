<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superadministrator_permissions = [

            // Transaction
            'transaction',
            'transaction.create',
            'transaction.edit',
            'transaction.destroy',
            'transaction.export',

            // Wallet
            'wallet',
            'wallet.edit',

            // User
            'user',
            'user.create',
            'user.edit',
            'user.destroy',

            // Setting
            'setting',
            'setting.edit',
        ];

        $user_permissions = [
            // Transaction
            'transaction.create',
        ];

        foreach ($superadministrator_permissions as $superadministrator_permission)
        {
            Permission::firstOrCreate(['name' => $superadministrator_permission]);
        }

        $roles = [
            'superadministrator' => $superadministrator_permissions,
            'user' => $user_permissions,
        ];

        foreach ($roles as $roleName => $rolePermissions)
        {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $superadmin = User::create([
            'uid' => 'SUPERADMIN9999',
            'name' => 'SUPERADMIN',
            'email' => 'superadmin@superadmin.com',
            'password' => Hash::make('123456789'),
            'join_date' => Carbon::today(),
            'status' => ActiveStatus::Active,
        ]);

        $superadmin->assignRole('superadministrator');

        $user = User::create([
            'uid' => 'USER',
            'name' => 'USER',
            'email' => 'user@user.com',
            'password' => Hash::make('123456789'),
            'join_date' => Carbon::today(),
            'status' => ActiveStatus::Active,
        ]);

        $user->assignRole('user');
    }
}
