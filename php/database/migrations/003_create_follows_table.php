<?php

/**
 * Migration for creating the 'follows' table.
 *
 * This table stores the relationships between users, indicating who follows whom.
 * It includes foreign key constraints to the 'users' table and a unique constraint
 * to prevent duplicate follow relationships.
 *
 * Assumes a `Schema` class (e.g., `App\Database\Schema` or a global `Schema` facade)
 * and a `Blueprint` class are available for database schema operations,
 * similar to common PHP frameworks.
 */
class CreateFollowsTable
{
    /**
     * Run the migrations.
     *
     * Creates the 'follows' table with 'follower_id', 'followed_id', and timestamps.
     *
     * @return void
     */
    public function up(): void
    {
        // Use the Schema facade to create the 'follows' table.
        // The callback receives a Blueprint object to define table columns and constraints.
        Schema::create('follows', function (Blueprint $table) {
            // Define 'follower_id' column as an unsigned big integer.
            // This column will store the ID of the user who is doing the following.
            $table->unsignedBigInteger('follower_id');

            // Define 'followed_id' column as an unsigned big integer.
            // This column will store the ID of the user who is being followed.
            $table->unsignedBigInteger('followed_id');

            // Add foreign key constraint for 'follower_id' referencing the 'id' column of the 'users' table.
            // If a user (follower) is deleted, all their follow relationships will also be deleted.
            $table->foreign('follower_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Add foreign key constraint for 'followed_id' referencing the 'id' column of the 'users' table.
            // If a user (followed) is deleted, all relationships where they were followed will also be deleted.
            $table->foreign('followed_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Ensure that a user can only follow another user once.
            // This creates a composite unique index on both columns.
            $table->unique(['follower_id', 'followed_id']);

            // Add 'created_at' and 'updated_at' columns for tracking creation and update times.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'follows' table if it exists.
     *
     * @return void
     */
    public function down(): void
    {
        // Use the Schema facade to drop the 'follows' table if it exists.
        Schema::dropIfExists('follows');
    }
}

// Note: In a real-world application, `Schema` and `Blueprint` classes would be
// defined in a framework or a custom database abstraction layer (e.g., `php/src/Database/Schema.php`).
// For this migration file, we assume they are globally accessible or properly
// imported/aliased by the migration runner.
// Example of how `Schema` and `Blueprint` might be structured (not part of this file):
/*
namespace App\Database;

use PDO;

class Schema
{
    protected static ?PDO $pdo = null;

    public static function setPdo(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public static function create(string $tableName, callable $callback): void
    {
        if (!self::$pdo) {
            throw new \RuntimeException("PDO connection not set for Schema.");
        }
        $blueprint = new Blueprint($tableName);
        $callback($blueprint);
        $sql = $blueprint->toSql();
        self::$pdo->exec($sql);
    }

    public static function dropIfExists(string $tableName): void
    {
        if (!self::$pdo) {
            throw new \RuntimeException("PDO connection not set for Schema.");
        }
        $sql = "DROP TABLE IF EXISTS `{$tableName}`";
        self::$pdo->exec($sql);
    }
}

class Blueprint
{
    protected string $tableName;
    protected array $columns = [];
    protected array $foreignKeys = [];
    protected array $uniqueKeys = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function unsignedBigInteger(string $columnName): self
    {
        $this->columns[] = "`{$columnName}` BIGINT UNSIGNED NOT NULL";
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function foreign(string $column): ForeignKeyConstraint
    {
        $fk = new ForeignKeyConstraint($column, $this->tableName);
        $this->foreignKeys[] = $fk;
        return $fk;
    }

    public function unique(array $columns): self
    {
        $quotedColumns = array_map(fn($col) => "`{$col}`", $columns);
        $this->uniqueKeys[] = "UNIQUE (" . implode(", ", $quotedColumns) . ")";
        return $this;
    }

    public function toSql(): string
    {
        $columnDefinitions = implode(",\n    ", $this->columns);
        $foreignKeyDefinitions = '';
        foreach ($this->foreignKeys as $fk) {
            $foreignKeyDefinitions .= ",\n    " . $fk->toSql();
        }
        $uniqueKeyDefinitions = '';
        if (!empty($this->uniqueKeys)) {
            $uniqueKeyDefinitions .= ",\n    " . implode(",\n    ", $this->uniqueKeys);
        }

        return "CREATE TABLE `{$this->tableName}` (\n    {$columnDefinitions}{$foreignKeyDefinitions}{$uniqueKeyDefinitions}\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }
}

class ForeignKeyConstraint
{
    protected string $column;
    protected string $tableName;
    protected string $references;
    protected string $onTable;
    protected ?string $onDelete = null;
    protected ?string $onUpdate = null;

    public function __construct(string $column, string $tableName)
    