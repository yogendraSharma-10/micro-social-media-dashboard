<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Config\Database;
use PDO;
use PDOException;

/**
 * Migration to create the 'posts' table.
 *
 * This table stores user-generated posts, including their content,
 * associated images, and references to parent posts for replies/comments.
 * It also includes counters for likes and comments.
 */
class CreatePostsTable
{
    /**
     * Runs the migration to create the 'posts' table.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            $pdo = Database::getConnection();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "
                CREATE TABLE IF NOT EXISTS posts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    content TEXT NOT NULL,
                    image_url VARCHAR(255) NULL,
                    parent_id INT NULL,
                    likes_count INT DEFAULT 0,
                    comments_count INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    
                    -- Foreign key constraint for the user who created the post
                    -- If a user is deleted, all their posts are also deleted (CASCADE).
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    
                    -- Self-referencing foreign key for replies/comments
                    -- If a parent post is deleted, its replies are also deleted (CASCADE).
                    FOREIGN KEY (parent_id) REFERENCES posts(id) ON DELETE CASCADE
                );
            ";
            $pdo->exec($sql);

            // Add indexes for performance on frequently queried columns
            $pdo->exec("CREATE INDEX idx_posts_user_id ON posts (user_id);");
            $pdo->exec("CREATE INDEX idx_posts_parent_id ON posts (parent_id);");

            echo "Migration 'CreatePostsTable' ran successfully (up).\n";
        } catch (PDOException $e) {
            error_log("Migration 'CreatePostsTable' failed (up): " . $e->getMessage());
            throw $e; // Re-throw to indicate migration failure
        }
    }

    /**
     * Reverses the migration by dropping the 'posts' table.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            $pdo = Database::getConnection();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "DROP TABLE IF EXISTS posts;";
            $pdo->exec($sql);

            echo "Migration 'CreatePostsTable' reversed successfully (down).\n";
        } catch (PDOException $e) {
            error_log("Migration 'CreatePostsTable' failed (down): " . $e->getMessage());
            throw $e; // Re-throw to indicate migration failure
        }
    }
}