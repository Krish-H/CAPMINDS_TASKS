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
        $results = $stmt->fetchAll();
        foreach ($results as &$row) {
            $row['name'] = EncryptionHelper::decrypt($row['name']);
            $row['age'] = EncryptionHelper::decrypt($row['age']);
            $row['gender'] = EncryptionHelper::decrypt($row['gender']);
            $row['phone'] = EncryptionHelper::decrypt($row['phone']);
            $row['address'] = EncryptionHelper::decrypt($row['address']);
        }
        return $results;
    }

    public function findById($id, $userId)
    {
        $query = "SELECT id,name,age,gender,phone,address FROM " . $this->table . " WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            $row['name'] = EncryptionHelper::decrypt($row['name']);
            $row['age'] = EncryptionHelper::decrypt($row['age']);
            $row['gender'] = EncryptionHelper::decrypt($row['gender']);
            $row['phone'] = EncryptionHelper::decrypt($row['phone']);
            $row['address'] = EncryptionHelper::decrypt($row['address']);
        }
        return $row;
    }

    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, name, age, gender, phone, address) VALUES (:user_id, :name, :age, :gender, :phone, :address)";
        $stmt = $this->conn->prepare($query);

        $name = EncryptionHelper::encrypt($data['name']);
        $age = EncryptionHelper::encrypt($data['age']);
        $gender = EncryptionHelper::encrypt($data['gender']);
        $phone = EncryptionHelper::encrypt($data['phone']);
        $address = EncryptionHelper::encrypt($data['address']);

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $userId, $data)
    {
        $fields = [];
        $allowedFieldsToEncrypt = ['name', 'age', 'gender', 'phone', 'address'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFieldsToEncrypt)) {
                $data[$key] = EncryptionHelper::encrypt($value);
            }
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
