<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

/**
 * Class User
 *
 * Represents the User model, handling database interactions for user-related data.
 * This model provides methods for CRUD operations, password management, and
 * relationship management (posts, followers, following).
 */
class User
{
    private ?int $id;
    private string $username;
    private string $email;
    private string $password_hash; // Storing hashed password
    private ?string $created_at;
    private ?string $updated_at;

    /**
     * User constructor.
     *
     * @param array $data Initial data to hydrate the user object.
     */
    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password_hash = $data['password_hash'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    /**
     * Get the database connection.
     *
     * @return PDO
     */
    private static function getDb(): PDO
    {
        return Database::getConnection();
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id The user ID.
     * @return User|null Returns a User object if found, null otherwise.
     */
    public static function find(int $id): ?User
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? new self($userData) : null;
    }

    /**
     * Find a user by their username.
     *
     * @param string $username The username.
     * @return User|null Returns a User object if found, null otherwise.
     */
    public static function findByUsername(string $username): ?User
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? new self($userData) : null;
    }

    /**
     * Find a user by their email.
     *
     * @param string $email The email address.
     * @return User|null Returns a User object if found, null otherwise.
     */
    public static function findByEmail(string $email): ?User
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? new self($userData) : null;
    }

    /**
     * Get all users from the database.
     *
     * @return User[] An array of User objects.
     */
    public static function all(): array
    {
        $db = self::getDb();
        $stmt = $db->query("SELECT * FROM users ORDER BY username ASC");
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => new self($data), $usersData);
    }

    /**
     * Create a new user in the database.
     *
     * @param array $data An associative array containing 'username', 'email', and 'password'.
     * @return User|null The newly created User object, or null on failure.
     * @throws PDOException If a database error occurs.
     */
    public static function create(array $data): ?User
    {
        $db = self::getDb();
        $db->beginTransaction();
        try {
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $db->prepare(
                "INSERT INTO users (username, email, password_hash, created_at, updated_at) 
                 VALUES (:username, :email, :password_hash, NOW(), NOW())"
            );
            $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
            $stmt->execute();

            $id = (int)$db->lastInsertId();
            $db->commit();
            return self::find($id);
        } catch (PDOException $e) {
            $db->rollBack();
            // Log the error or handle it appropriately
            error_log("Error creating user: " . $e->getMessage());
            throw $e; // Re-throw for controller to handle
        }
    }

    /**
     * Update an existing user's data.
     *
     * @param int $id The ID of the user to update.
     * @param array $data An associative array of data to update (e.g., 'username', 'email', 'password').
     *                    If 'password' is provided, it will be hashed.
     * @return bool True on success, false on failure.
     * @throws PDOException If a database error occurs.
     */
    public static function update(int $id, array $data): bool
    {
        $db = self::getDb();
        $db->beginTransaction();
        try {
            $setClauses = [];
            $params = [':id' => $id];

            if (isset($data['username'])) {
                $setClauses[] = 'username = :username';
                $params[':username'] = $data['username'];
            }
            if (isset($data['email'])) {
                $setClauses[] = 'email = :email';
                $params[':email'] = $data['email'];
            }
            if (isset($data['password'])) {
                $setClauses[] = 'password_hash = :password_hash';
                $params[':password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            if (empty($setClauses)) {
                return false; // Nothing to update
            }

            $setClauses[] = 'updated_at = NOW()';

            $sql = "UPDATE users SET " . implode(', ', $setClauses) . " WHERE id = :id";
            $stmt = $db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $result = $stmt->execute();
            $db->commit();
            return $result;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error updating user (ID: $id): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a user from the database.
     *
     * @param int $id The ID of the user to delete.
     * @return bool True on success, false on failure.
     * @throws PDOException If a database error occurs.
     */
    public static function delete(int $id): bool
    {
        $db = self::getDb();
        $db->beginTransaction();
        try {
            // Consider soft deletes in a real production app
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $db->commit();
            return $result;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error deleting user (ID: $id): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a plain text password against the stored hash.
     *
     * @param string $password The plain text password.
     * @return bool True if the password matches, false otherwise.
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Get all posts made by this user.
     *
     * @return Post[] An array of Post objects.
     */
    public function getPosts(): array
    {
        if (!$this->id) {
            return [];
        }
        return Post::findByUserId($this->id);
    }

    /**
     * Get users who are following this user.
     *
     * @return User[] An array of User objects (followers).
     */
    public function getFollowers(): array
    {
        if (!$this->id) {
            return [];
        }
        $db = self::getDb();
        $stmt = $db->prepare(
            "SELECT u.* FROM users u 
             JOIN follows f ON u.id = f.follower_id 
             WHERE f.following_id = :user_id"
        );
        $stmt->bindParam(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $followersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => new self($data), $followersData);
    }

    /**
     * Get users that this user is following.
     *
     * @return User[] An array of User objects (following).
     */
    public function getFollowing(): array
    {
        if (!$this->id) {
            return [];
        }
        $db = self::getDb();
        $stmt = $db->prepare(
            "SELECT u.* FROM users u 
             JOIN follows f ON u.id = f.following_id 
             WHERE f.follower_id = :user_id"
        );
        $stmt->bindParam(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $followingData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => new self($data), $followingData);
    }

    /**
     * Make this user follow another user.
     *
     * @param int $targetUserId The ID of the user to follow.
     * @return bool True on success, false if already following or on failure.
     * @throws PDOException If a database error occurs.
     */
    public function follow(int $targetUserId): bool
    {
        if (!$this->id || $this->id === $targetUserId) {
            return false; // Cannot follow self or invalid user
        }

        if ($this->isFollowing($targetUserId)) {
            return false; // Already following
        }

        $db = self::getDb();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                "INSERT INTO follows (follower_id, following_id, created_at) 
                 VALUES (:follower_id, :following_id, NOW())"
            );
            $stmt->bindParam(':follower_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':following_id', $targetUserId, PDO::PARAM_INT);
            $result = $stmt->execute();
            $db->commit();
            return $result;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error user {$this->id} following user $targetUserId: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Make this user unfollow another user.
     *
     * @param int $targetUserId The ID of the user to unfollow.
     * @return bool True on success, false if not following or on failure.
     * @throws PDOException If a database error occurs.
     */
    public function unfollow(int $targetUserId): bool
    {
        if (!$this->id || $this->id === $targetUserId) {
            return false;
        }

        if (!$this->isFollowing($targetUserId)) {
            return false; // Not following
        }

        $db = self::getDb();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
