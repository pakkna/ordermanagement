<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'username' => '01624389711',
            'mobile' => '01624389711',  // Change this to the desired mobile number
            'password' => Hash::make('654321'),  // Set a secure password
            'usertype' => 'admin',  // You can set the role here if using a role management system like Spatie
            'registered_by' => 'admin'
        ]);

        // Assign the 'admin' role using Spatie (if you're using that package)
        $admin->assignRole('admin');
    }
}
