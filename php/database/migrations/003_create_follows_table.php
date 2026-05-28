<?php

/**
 * Migration to create the 'follows' table.
 *
 * This table stores the relationships between users, indicating who follows whom.
 * It includes foreign keys to the 'users' table and a unique constraint
 * to prevent duplicate follow relationships.
 */
class CreateFollowsTable
{
    /**
     * Runs the migration to create the 'follows' table.
     *
     * @param PDO $pdo The PDO database connection instance.
     * @return void
     */
    public function up(PDO $pdo): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS follows (
                id INT AUTO_INCREMENT PRIMARY KEY,
                follower_id INT NOT NULL COMMENT 'The ID of the user who is following.',
                following_id INT NOT NULL COMMENT 'The ID of the user being followed.',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                -- Foreign key constraint for the follower_id
                CONSTRAINT fk_follows_follower_id
                    FOREIGN KEY (follower_id) REFERENCES users(id)
                    ON DELETE CASCADE,
                
                -- Foreign key constraint for the following_id
                CONSTRAINT fk_follows_following_id
                    FOREIGN KEY (following_id) REFERENCES users(id)
                    ON DELETE CASCADE,
                
                -- Ensure a user can only follow another user once
                UNIQUE KEY unique_follower_following (follower_id, following_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql);

        // Add explicit indexes for performance on foreign key columns
        // (though unique_follower_following implies an index on both,
        // separate indexes can be beneficial for lookups where only one ID is known).
        $pdo->exec("CREATE INDEX idx_follows_follower_id ON follows (follower_id);");
        $pdo->exec("CREATE INDEX idx_follows_following_id ON follows (following_id);");

        echo "Migration 'CreateFollowsTable' UP completed successfully.\n";
    }

    /**
     * Reverses the migration by dropping the 'follows' table.
     *
     * @param PDO $pdo The PDO database connection instance.
     * @return void
     */
    public function down(PDO $pdo): void
    {
        $sql = "DROP TABLE IF EXISTS follows;";
        $pdo->exec($sql);
        echo "Migration 'CreateFollowsTable' DOWN completed successfully.\n";
    }
}