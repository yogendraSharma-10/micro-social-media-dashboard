<?php

// php/src/Routes/api.php

/**
 * API Routes for the Micro Social Media Dashboard.
 *
 * This file defines all the API endpoints for the application.
 * It's designed to be included by the main `index.php` file, which
 * will then dispatch requests to the appropriate controller methods.
 *
 * Each route is defined as an associative array with the following keys:
 * - 'method': The HTTP method (e.g., 'GET', 'POST', 'PUT', 'DELETE').
 * - 'path': The URL path for the route (e.g., '/api/posts'). Path parameters
 *           are denoted by `{paramName}` (e.g., `/api/users/{id}`).
 * - 'controller': The fully qualified class name of the controller or service.
 * - 'action': The method name within the controller/service to execute.
 * - 'middleware': An optional array of middleware to apply (e.g., ['auth']).
 *                 Middleware would typically handle authentication, authorization,
 *                 request validation, etc., before the controller action is invoked.
 *
 * This setup assumes a basic routing mechanism in `public/index.php` that
 * iterates through these routes, matches the incoming request, extracts
 * path parameters, applies middleware, and then dispatches to the controller.
 */

// Ensure controllers and services are loaded or autoloaded.
// In a production environment, Composer's autoloader handles these `use` statements.
use App\Controllers\PostController;
use App\Controllers\UserController;
use App\Services\NotificationService; // Assuming NotificationService methods are directly callable or wrapped by a controller

// Define the API routes
return [
    // --- Authentication & User Management ---
    [
        'method' => 'POST',
        'path' => '/api/register',
        'controller' => UserController::class,
        'action' => 'register',
        'middleware' => [], // No authentication required for registration
    ],
    [
        'method' => 'POST',
        'path' => '/api/login',
        'controller' => UserController::class,
        'action' => 'login',
        'middleware' => [], // No authentication required for login
    ],
    [
        'method' => 'GET',
        'path' => '/api/me',
        'controller' => UserController::class,
        'action' => 'me',
        'middleware' => ['auth'], // Requires authentication to get the current authenticated user's profile
    ],
    [
        'method' => 'GET',
        'path' => '/api/users/{id}',
        'controller' => UserController::class,
        'action' => 'show',
        'middleware' => ['auth'], // User profiles are generally public, but for this app, let's assume auth is needed
    ],
    [
        'method' => 'GET',
        'path' => '/api/users/{id}/posts',
        'controller' => UserController::class,
        'action' => 'getUserPosts',
        'middleware' => ['auth'], // Get posts by a specific user
    ],
    [
        'method' => 'POST',
        'path' => '/api/users/{id}/follow',
        'controller' => UserController::class,
        'action' => 'follow',
        'middleware' => ['auth'], // Must be authenticated to follow another user
    ],
    [
        'method' => 'DELETE',
        'path' => '/api/users/{id}/unfollow',
        'controller' => UserController::class,
        'action' => 'unfollow',
        'middleware' => ['auth'], // Must be authenticated to unfollow another user
    ],
    [
        'method' => 'GET',
        'path' => '/api/users/{id}/followers',
        'controller' => UserController::class,
        'action