<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminStaffSeeder extends Seeder
{
    private const PASSWORD = '12345678';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {
            $this->upsertStaffAccount(
                name: 'مدير النظام',
                email: 'admin@healthnexus.test',
                roleName: 'super_admin'
            );

            $this->upsertStaffAccount(
                name: 'سكرتيرة المركز',
                email: 'secretary@healthnexus.test',
                roleName: 'secretary'
            );
        });

        $this->command?->info('تم إنشاء حساب المدير والسكرتيرة. كلمة المرور: '.self::PASSWORD);
    }

    private function upsertStaffAccount(string $name, string $email, string $roleName): User
    {
        $role = Role::findOrCreate($roleName, 'web');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'roles_name' => [$roleName],
                'Status' => '1',
                'email_verified_at' => now(),
                'verification_code' => null,
            ]
        );

        $user->syncRoles([$role]);

        return $user;
    }
}
