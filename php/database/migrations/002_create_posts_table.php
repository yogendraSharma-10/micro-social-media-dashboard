<?php

/**
 * Migration: CreatePostsTable
 *
 * This migration creates the 'posts' table in the database.
 * The 'posts' table stores all user-generated content, including text posts,
 * optional image URLs, and references for replies/comments.
 * It also includes counters for likes and comments to optimize retrieval.
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
        // SQL statement to create the 'posts' table.
        // - id: Primary key, auto-incrementing.
        // - user_id: Foreign key linking to the 'users' table, indicating the post's author.
        //            ON DELETE CASCADE ensures posts are deleted if the user is deleted.
        // - content: The main text content of the post.
        // - image_url: Optional URL for an image attached to the post.
        // - parent_id: Optional foreign key linking to another post, used for replies/comments.
        //              ON DELETE CASCADE ensures replies are deleted if the parent post is deleted.
        // - likes_count: Counter for the number of likes a post has received. Defaults to 0.
        // - comments_count: Counter for the number of comments/replies a post has received. Defaults to 0.
        // - created_at: Timestamp for when the post was created.
        // - updated_at: Timestamp for when the post was last updated.
        // - Indexes are added for foreign keys to improve query performance.
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
                
                INDEX idx_posts_user_id (user_id),
                INDEX idx_posts_parent_id (parent_id),
                
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (parent_id) REFERENCES posts(id) ON DELETE CASCADE
            );
        ";

        // In a real application, you would execute this SQL using a database connection.
        // For example:
        // (new DatabaseConnector())->getConnection()->exec($sql);
        // For demonstration purposes, we'll just echo the SQL.
        echo "Executing UP migration for CreatePostsTable...\n";
        // echo $sql . "\n"; // Uncomment to see the SQL output during migration run
    }

    /**
     * Reverses the migration by dropping the 'posts' table.
     *
     * @return void
     */
    public function down(): void
    {
        // SQL statement to drop the 'posts' table if it exists.
        $sql = "DROP TABLE IF EXISTS posts;";

        // In a real application, you would execute this SQL using a database connection.
        // For example:
        // (new DatabaseConnector())->getConnection()->exec($sql);
        // For demonstration purposes, we'll just echo the SQL.
        echo "Executing DOWN migration for CreatePostsTable...\n";
        // echo $sql . "\n"; // Uncomment to see the SQL output during migration run
    }
}

// Note: This file defines the migration logic. A separate migration runner
// would be responsible for instantiating this class and calling its `up()` or `down()` methods.
// The `DatabaseConnector` is a placeholder for your actual database connection logic.
?>