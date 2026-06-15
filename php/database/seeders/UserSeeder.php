<?php

namespace Database\Seeders;

use PDO;
use PDOException;

/**
 * UserSeeder
 *
 * This seeder populates the 'users' table with initial data.
 * It's designed for development and testing environments to quickly
 * set up a baseline of users for the Micro Social Media Dashboard.
 *
 * It uses direct PDO for database interaction, assuming a custom
 * lightweight PHP setup without a full-fledged ORM or framework-specific
 * database facade.
 */
class UserSeeder
{
    /**
     * Run the database seeds.
     *
     * This method connects to the database, truncates the 'users' table
     * (for idempotent re-seeding in development), and inserts sample user data.
     * Passwords are securely hashed using PASSWORD_BCRYPT.
     *
     * @return void
     */
    public function run(): void
    {
        echo "Starting User Seeder...\n";

        // Load database configuration from the main config file.
        // Adjust the path as necessary based on where this seeder is executed from.
        $dbConfig = require __DIR__ . '/../../src/Config/database.php';

        // Construct DSN for PDO connection.
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Default fetch mode to associative array
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Disable emulation for better security and performance
        ];

        try {
            // Establish a new PDO database connection.
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
            echo "Successfully connected to the database.\n";
        } catch (PDOException $e) {
            // Log and terminate if database connection fails.
            die("ERROR: Could not connect to the database. " . $e->getMessage() . "\n");
        }

        // --- Truncate existing users table for clean re-seeding ---
        // This is useful for development but should be handled carefully in production.
        // Temporarily disable foreign key checks to allow truncation if other tables
        // (like posts, follows) have foreign keys referencing users.
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            $pdo->exec("TRUNCATE TABLE users;");
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            echo "Truncated 'users' table for a clean seed.\n";
        } catch (PDOException $e) {
            echo "WARNING: Could not truncate 'users' table. Error: " . $e->getMessage() . "\n";
            echo "Continuing seeding, but duplicates might occur if unique constraints are not handled.\n";
        }

        // Define an array of sample user data.
        // Passwords are hashed for security.
        // `profile_picture` uses pravatar.cc for random avatars.
        $users = [
            [
                'username' => 'john_doe',
                'email' => 'john.doe@example.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'bio' => 'Passionate about web development and open source. Always learning!',
                'profile_picture' => 'https://i.pravatar.cc/150?img=1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'jane_smith',
                'email' => 'jane.smith@example.com',
                'password' => password_hash('securepass', PASSWORD_BCRYPT),
                'bio' => 'Loves photography, hiking, and exploring new places. Nature enthusiast.',
                'profile_picture' => 'https://i.pravatar.cc/150?img=2',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'username' => 'alice_wonder',
                'email' => 'alice.wonder@example.com',
                'password' => password_hash('wonderland', PASSWORD_BCRYPT),
                'bio' => 'Full-stack developer with a keen eye for design and user experience.',
                'profile_picture' => 'https://i.pravatar.cc/150?img=3',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'username' => 'bob_builder',
                'email' => 'bob.builder@example.com',
                'password' => password_hash('canwefixit', PASSWORD_BCRYPT),
                'bio' => 'Building scalable applications and robust APIs. DevOps enthusiast.',
                'profile_picture' => 'https://i.pravatar.cc/150?img