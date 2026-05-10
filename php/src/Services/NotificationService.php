<?php

namespace App\Services;

use App\Config\Database;
use PDO;
use PDOException;

/**
 * Class NotificationService
 * Handles all business logic related to creating, retrieving, and managing user notifications.
 * This service interacts directly with the database to store and fetch notification data.
 */
class NotificationService
{
    private PDO $db;

    // Define constants for notification types for better maintainability and readability
    public const TYPE_FOLLOW = 'follow';
    public const TYPE_POST_LIKED = 'post_liked';
    public const TYPE_POST_COMMENTED = 'post_commented';
    // Add more types as the application grows, e.g., TYPE_MENTION, TYPE_SHARE, etc.

    /**
     * NotificationService constructor.
     * Initializes the database connection using the static `getConnection` method from the Database config.
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Creates a new notification entry in the database.
     *
     * @param int    $userId The ID of the user who is to receive this notification.
     * @param string $type   The type of notification (e.g., 'follow', 'post_liked').
     *                       Should ideally be one of the `TYPE_` constants defined in this class.
     * @param array  $data   An associative array containing additional context for the notification.