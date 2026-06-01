<?php

class RefreshToken
{
    private $conn;
    private $table = 'refresh_tokens';

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($userId, $token, $expiresAt)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, refresh_token, expires_at) VALUES (:user_id, :refresh_token, :expires_at)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':refresh_token', $token);
        $stmt->bindParam(':expires_at', $expiresAt);

        return $stmt->execute();
    }

    public function findByToken($token)
    {
        $query = "SELECT  user_id,expires_at FROM " . $this->table . " WHERE refresh_token = :refresh_token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':refresh_token', $token);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function delete($token)
    {
        $query = "DELETE FROM " . $this->table . " WHERE refresh_token = :refresh_token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':refresh_token', $token);
        
        return $stmt->execute();
    }

    public function deleteByUserId($userId)
    {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }
}
