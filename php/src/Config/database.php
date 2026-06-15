<?php

/**
 * Database Configuration File
 *
 * This file defines the database connection parameters for the Micro Social Media Dashboard.
 * It loads sensitive credentials from environment variables to ensure security and flexibility
 * across different deployment environments (development, staging, production).
 *
 * Best practice: Never hardcode credentials directly in this file.
 * Ensure your .env file is properly configured and not committed to version control.
 *
 * For a microservices architecture, each service typically has its own dedicated database.
 * This configuration is specifically for the 'social_media_db'. Other services like
 * 'Real-time Collaborative Whiteboard' or 'Multi-vendor E-commerce Marketplace' would
 * have their own distinct database configurations.
 */

// Ensure environment variables are loaded. In a real application, a library like
// 'vlucas/phpdotenv' would be used and initialized in public/index.php.
// For simplicity, we assume getenv() will work if the web server or CLI environment
// has these variables set, or if a simple dotenv loader is included earlier.

return [
    'driver'    => getenv('DB_DRIVER') ?? 'mysql',
    'host'      => getenv('DB_HOST') ?? 'localhost',
    'port'      => getenv('DB_PORT') ?? '3306',
    'database'  => getenv('DB_DATABASE') ?? 'social_media_db', // Specific to this project
    'username'  => getenv('DB_USERNAME') ?? 'root',
    'password'  => getenv('DB_PASSWORD') ?? '',
    'charset'   => getenv('DB_CHARSET') ?? 'utf8mb4',
    'collation' => getenv('DB_COLLATION') ?? 'utf8mb4_unicode_ci',
    'prefix'    => getenv('DB_PREFIX') ?? '',
    'options'   => [
        // Enable persistent connections for better performance (use with caution in some environments)
        // PDO::ATTR_PERSISTENT => true,

        // Set default fetch mode to associative array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // Throw exceptions on errors, which is crucial for robust error handling
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        // Disable emulation of prepared statements for better security and performance
        PDO::EMULATE_PREPARES => false,

        // Set character set for the connection
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
    ],
];