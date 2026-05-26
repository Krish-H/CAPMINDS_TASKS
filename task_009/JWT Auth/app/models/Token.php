<?php

class Token
{
    private $conn;
    private $table = 'user_tokens';

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function store($userId, $jti)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, token) VALUES (:user_id, :token)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $jti);

        return $stmt->execute();
    }

    public function isValid($userId, $jti)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE user_id = :user_id AND token = :token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $jti);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    public function revokeAll($userId)
    {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }
}
