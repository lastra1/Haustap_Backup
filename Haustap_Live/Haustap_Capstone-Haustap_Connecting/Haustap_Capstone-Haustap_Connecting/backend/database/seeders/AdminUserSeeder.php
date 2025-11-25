<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure Admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Create or update an admin user
        $user = User::updateOrCreate(
            ['email' => 'admin@haustap.local'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Attach Admin role
        $user->role()->associate($adminRole);
        $user->save();
    }
}

