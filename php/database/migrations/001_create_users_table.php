<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\Blueprint;

/**
 * Migration for creating the 'users' table.
 *
 * This migration sets up the foundational table for user accounts in the Micro Social Media Dashboard.
 * It includes essential fields for user identification, authentication, and basic profile information.
 *
 * In a production environment, ensure your database connection and schema builder
 * (e.g., a custom implementation or a framework's ORM/DBAL) are properly configured
 * to execute these operations.
 */
class CreateUsersTable
{
    /**
     * Runs the migration to create the 'users' table.
     *
     * @return void
     */
    public function up(): void
    {
        // Assuming a `Schema` class exists with static methods for database schema manipulation,
        // similar to what's found in frameworks like Laravel.
        // If not using such a framework, this would typically involve raw SQL queries
        // executed via PDO or a database abstraction layer.
        // Example: `(new PDO(...))->exec("CREATE TABLE users (...)");`

        // Define the schema for the 'users' table.
        // This table stores core user information for the social media platform.
        Schema::create('users', function (Blueprint $table) {
            // Primary Key: Auto-incrementing unsigned big integer.
            // This is a standard primary key for most relational databases.
            $table->id();

            // User's unique username. Used for login and public identification.
            // Must be unique to prevent conflicts. Max length 255 is standard for string.
            $table->string('username', 255)->unique();

            // User's unique email address. Used for login, password recovery, and notifications.
            // Must be unique. Max length 255 is standard.
            $table->string('email', 255)->unique();

            // Hashed password for user authentication.
            // Storing plain text passwords is a severe security vulnerability.
            // Always hash passwords before storing them.
            // A common length for hashed passwords (e.g., bcrypt) is around 60-255 characters.
            $table->string('password', 255);

            // Optional biography or "about me" section for the user's profile.
            // `