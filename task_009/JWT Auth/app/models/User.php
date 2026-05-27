<?php

class User
{
    private $conn;
    private $table = 'users';

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($name, $email, $password)
    {
        $query = "INSERT INTO " . $this->table . " (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashed_password);

        try {
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                return -1; 
            }
            throw $e;
        }
    }

    public function findByEmail($email)
    {
        $query = "SELECT id, name, email, password FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch();
    }
    public function findById($id)
    {
        $query = "SELECT id, name, email FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }
}
