<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * Include the database configuration to get the PDO connection.
 * This assumes `database.php` defines a function like `getDbConnection()`
 * that returns a configured PDO instance.
 */
require_once __DIR__ . '/../Config/database.php';

/**
 * Class Post
 *
 * Represents a social media post in the application.
 * Handles database interactions for posts, including CRUD operations
 * and relationships (e.g., with the User model).
 */
class Post
{
    /**
     * @var int The unique identifier for the post.
     */
    public int $id;

    /**
     * @var int The ID of the user who created the post.
     */
    public int $user_id;

    /**
     * @var string The main content/text of the post.
     */
    public string $content;

    /**
     * @var string|null The URL to an image associated with the post, if any.
     */
    public ?string $image_url;

    /**
     * @var string The timestamp when the post was created.
     */
    public string $created_at;

    /**
     * @var string The timestamp when the post was last updated.
     */
    public string $updated_at;

    /**
     * Post constructor.
     *
     * @param int $id The unique identifier for the post.
     * @param int $user_id The ID of the user who created the post.
     * @param string $content The main content/text of the post.
     * @param string|null $image_url The URL to an image associated with the post, if any.
     * @param string $created_at The timestamp when the post was created.
     * @param string $updated_at The timestamp when the post was last updated.
     */
    public function __construct(int $id, int $user_id, string $content, ?string $image_url, string $created_at, string $updated_at)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->content = $content;
        $this->image_url = $image_url;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    /**
     * Establishes and returns a PDO database connection.
     *
     * This method assumes `getDbConnection()` is a global function defined
     * in `php/src/Config/database.php` that returns a PDO instance.
     *
     * @return PDO The PDO database connection object.
     * @throws PDOException If the connection fails.
     */
    private static function getConnection(): PDO
    {
        // Ensure the function exists before calling it.
        if (!function_exists('getDbConnection')) {
            throw new PDOException("Database connection function 'getDbConnection' not found.");
        }
        return getDbConnection();
    }

    /**
     * Finds a single post by its unique ID.
     *
     * @param int $id The ID of the post to find.
     * @return Post|null The Post object if found, otherwise null.
     */
    public static function find(int $id): ?Post
    {
        try {
            $db = self::getConnection();
            $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                return new self(
                    $data['id'],
                    $data['user_id'],
                    $data['content'],
                    $data['image_url'],
                    $data['created_at'],
                    $data['updated_at']
                );
            }
            return null;
        } catch (PDOException $e) {
            // In a production environment, consider using a dedicated logging library.
            error_log("Error finding post with ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves all posts from the database, optionally filtered by user ID.
     * Posts are ordered by creation date in descending order.
     *
     * @param int|null $userId Optional. If provided, only posts by this user will be returned.
     * @return Post[] An array of Post objects. Returns an empty array if no posts are found or on error.
     */
    public static function all(?int $userId = null): array
    {
        try {
            