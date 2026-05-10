<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use App\Config\Database;
use PDO;

/**
 * UserController
 * Handles user-related API requests such as registration, login, profile management,
 * and social interactions like following/unfollowing.
 */
class UserController
{
    private PDO $db;
    private NotificationService $notificationService;

    /**
     * Constructor initializes database connection and NotificationService.
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->notificationService = new NotificationService();
    }

    /**
     * Helper to send JSON responses.
     *