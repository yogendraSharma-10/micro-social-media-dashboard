<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\PostController;
use App\Controllers\UserController;
use App\Controllers\NotificationController; // Assuming this controller will be created to handle notification-related API requests
use App\Core\Router; // Assuming a custom Router class exists in App\Core, responsible for dispatching requests

/**
 * API Routes for the Micro Social Media Dashboard.
 *
 * This file defines all the API endpoints for the application, mapping
 * HTTP methods and URIs to specific controller actions.
 *
 * It's designed to be included by the main application entry point (e.g., public/index.php)
 * which would pass an instance of the Router