<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleCreateSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        // Call the Role Create Seeder
        $this->call(RoleCreateSeeder::class);
        // Call the AdminUserSeeder
        $this->call(AdminUserSeeder::class);
    }
}
