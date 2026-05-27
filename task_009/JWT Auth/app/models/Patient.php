<?php

class Patient
{
    private $conn;
    private $table = 'patients';

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll($userId)
    {
        $query = "SELECT id,name,age,gender,phone,address FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id, $userId)
    {
        $query = "SELECT id,name,age,gender,phone,address FROM " . $this->table . " WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, name, age, gender, phone, address) VALUES (:user_id, :name, :age, :gender, :phone, :address)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':age', $data['age']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $userId, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fieldsStr = implode(', ', $fields);

        $query = "UPDATE " . $this->table . " SET $fieldsStr WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $data['id'] = $id;
        $data['user_id'] = $userId;
        
        return $stmt->execute($data);
    }

    public function delete($id, $userId)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
