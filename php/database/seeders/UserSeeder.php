<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method populates the 'users' table with initial data.
     * It creates a few sample users with hashed passwords and unique details
     * to facilitate testing and initial development of the social media dashboard.
     *
     * @return void
     */
    public function run(): void
    {
        // Clear existing users to prevent duplicates on re-seeding
        // In a production environment, you might want to be more careful
        // about clearing data, perhaps only doing it in development/testing.
        // User::truncate(); // Uncomment if you want to clear all users before seeding

        // Create a primary admin/test user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin_user',
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Hashed password for 'password'
                'profile_picture' => 'https://i.pravatar.cc/150?img=68', // Example avatar
                'bio' => 'Administrator of the Micro Social Media Dashboard.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create a few more regular users
        $users = [
            [
                'username' => 'john_doe',
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password'),
                'profile_picture' => 'https://i.pravatar.cc/150?img=1',
                'bio' => 'Passionate about tech and open source. Sharing my thoughts here!',
            ],
            [
                'username' => 'jane_smith',
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password'),
                'profile_picture' => 'https://i.pravatar.cc/150?img=2',
                'bio' => 'Lover of nature, photography, and good coffee.',
            ],
            [
                'username' => 'alex_williams',
                'name' => 'Alex Williams',
                'email' => 'alex.williams@example.com',
                'password' => Hash::make('password'),
                'profile_picture' => 'https://i.pravatar.cc/150?img=3',
                'bio' => 'Software engineer, aspiring musician, and avid reader.',
            ],
            [
                'username' => 'emily_jones',
                'name' => 'Emily Jones',
                'email' => 'emily.jones@example.com',
                'password' => Hash::make('password'),
                'profile_picture' => 'https://i.pravatar.cc/150?img=4',
                'bio' => 'Digital artist exploring new mediums and styles.',
            ],
            [
                'username' => 'michael_brown',
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'password' => Hash::make('password'),
                'profile_picture' => 'https://i.pravatar.cc/150?img=5',
                'bio' => 'Sports enthusiast and fitness coach. Stay active!',
            ],
            [
                'username' => 'sarah_davis',
                'name' => 'Sarah Davis',
                'email' => 'sarah.davis@example.com',
                'password' => Hash::make('password'),
                'profile_picture' => 'https://i.pravatar.cc/150?img=6',
                'bio' => 'Food blogger and culinary adventurer. Follow for recipes!',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']], // Check for existing user by email
                array_merge($userData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // You can also use factories for more complex or numerous data generation
        // Example: User::factory()->count(50)->create();
        // This would require setting up a UserFactory.
    }
}