<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Ronaldo B. Banas', 
            'email' => 'rbbanas@depdev.gov.ph',
            'email_verified_at' => date('Y-m-d H:i:s'),
            'password' => bcrypt('password'),
            'role_id' => 1,
            'agency_id' => 1,
        ]);
    }
}
