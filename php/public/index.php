<?php

/**
 * Micro Social Media Dashboard - PHP Backend Entry Point
 *
 * This file serves as the main entry point for all API requests to the PHP backend.
 * It handles:
 *  - Autoloading Composer dependencies.
 *  - Loading environment variables from the .env file.
 *  - Configuring error reporting based on the application environment.
 *  - Setting up Cross-Origin Resource Sharing (CORS) headers.
 *  - Initializing the database connection.
 *  - Dispatching incoming HTTP requests to the appropriate controller methods
 *    using a simple custom router.
 *
 * Best Practices:
 *  - Uses Dotenv for environment variable management.
 *  - Basic error reporting configuration for development vs. production.
 *