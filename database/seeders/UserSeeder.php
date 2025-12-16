<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'phone_number' => '1234567890',
            'password' => bcrypt('password123')
        ]);

        Profile::create([
            'user_id' => $user->id,
            'first_name' => 'Ali',
            'last_name' => 'Mdaghmesh',
            'date_of_birth' => '1990-05-15',
            'role' => 'admin',
            'verified' => true,
            'identity_image_url' => 'images/identities/identity1.jpg',
            'profile_image_url' => 'images/profiles/profile1.jpg',
        ]);

        $user = User::factory()->create([
            'phone_number' => '1234567891',
            'password' => bcrypt('password123')
        ]);

        Profile::create([
            'user_id' => $user->id,
            'first_name' => 'Aghed',
            'last_name' => 'Al-Khateb',
            'date_of_birth' => '1998-09-04',
            'role' => 'tenant',
            'verified' => true,
            'identity_image_url' => 'images/identities/identity2.jpg',
            'profile_image_url' => 'images/profiles/profile2.jpg',
        ]);

        $user = User::factory()->create([
            'phone_number' => '1234567892',
            'password' => bcrypt('password123')
        ]);

        Profile::create([
            'user_id' => $user->id,
            'first_name' => 'Ibrahim',
            'last_name' => 'Al-Ibrahim',
            'date_of_birth' => '1980-10-02',
            'role' => 'owner',
            'verified' => true,
            'identity_image_url' => 'images/identities/identity3.jpg',
            'profile_image_url' => 'images/profiles/profile3.jpg',
        ]);
    }
}
