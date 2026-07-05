<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::first();

        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@digibank.com',
            'password' => bcrypt('12345678'),
        ]);

        $admin->assignRole($adminRole);
    }
}
