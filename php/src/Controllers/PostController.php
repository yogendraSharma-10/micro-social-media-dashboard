<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Services\NotificationService;
use App\Config\Database;
use PDO;

/**
 * A simple Request class for demonstration purposes.
 * In a real-world application, this would typically be provided by a framework
 * (e.g., Symfony's Request, Laravel's Request) and would be much more robust,
 * handling various input types, validation, and authentication context.
 */
class Request
{
    private array $params;
    private array $body;
    private array $headers;

    public function __construct()
    {
        $this->params = $_GET;
        $this->