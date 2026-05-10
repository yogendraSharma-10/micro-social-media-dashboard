<?php

// php/database/migrations/001_create_users_table.php

/**
 * This file is part of the Micro Social Media Dashboard project.
 * It defines the migration to create the 'users' table in the database.
 *
 * This migration assumes the existence of a custom database schema builder
 * (e.g., `App\Database\Schema\Schema` and `App\Database\Schema\Blueprint`)
 * that provides methods for creating and modifying database tables, similar
 * to popular PHP frameworks.
 */

// Ensure these classes are properly autoloaded based on your Composer setup.
use App\Database\Schema\Blueprint;
use App\Database\Schema\Schema;

/**
 * Migration class to create and drop the 'users' table.
 *
 * The 'users' table stores all registered user accounts for the social media
 * platform. It includes essential fields for authentication, profile management,
 * and tracking user activity.
 */
class CreateUsersTable
{
    /**
     * Run the migrations.
     *
     * This method is executed when the migration is run. It defines the
     * structure of the 'users' table, including columns for user identification,
     * authentication credentials, and basic profile information.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key: A unique, auto-incrementing identifier for each user.
            $table->id(); // Maps to BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // User Credentials & Authentication
            $table->string('username')->unique()->comment('Unique username for user login and public display.');
            $table->string('email')->unique()->comment('Unique email address for user login, notifications, and password recovery.');
            $table->timestamp('email_verified_at')->nullable()->comment('Timestamp indicating when the user\'s email address was verified.');
            $table->string('password')->comment('Hashed password for secure user authentication.');

            // Profile Information
            $table->string('profile_picture')->nullable()->default('default_avatar.png')->comment('URL or path to the user\'s profile picture. Defaults to a generic avatar.');
            $table->text('bio')->nullable()->comment('A short biography or description provided by the user.');

            // Session Management
            $table->string('remember_token', 100)->nullable()->comment('Token used for "remember me" functionality, allowing persistent user sessions.');

            // Timestamps: Automatically manage creation and update dates.
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns (TIMESTAMP DEFAULT CURRENT_TIMESTAMP, TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method is executed when the migration is rolled back. It drops
     * the 'users' table if it exists, effectively reverting the changes
     * made by the `up()` method.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}