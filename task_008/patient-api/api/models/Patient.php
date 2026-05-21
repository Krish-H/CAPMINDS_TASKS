<?php

class Patient {
    private $conn;
    private $table_name = "patients";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get All Patients
    public function getAllPatients($limit = 5, $offset = 0) {

        $query = "SELECT 
                    id,
                    name,
                    age,
                    gender,
                    phone,
                    created_at
                  FROM " . $this->table_name . "
                  ORDER BY id DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);

        $stmt->execute();

        $result = $stmt->get_result();

        $patients = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $patients;
    }

    // Get Single Patient By ID
    public function getPatientById($id) {

        $query = "SELECT 
                    id,
                    name,
                    age,
                    gender,
                    phone,
                    created_at
                  FROM " . $this->table_name . "
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $patient = $result->fetch_assoc();

        $stmt->close();

        return $patient;
    }

    // Create Patient
    public function createPatient($data) {

        $query = "INSERT INTO " . $this->table_name . "
                  (name, age, gender, phone)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $name = trim($data['name'] ?? '');
        $age = (int) ($data['age'] ?? 0);
        $gender = trim($data['gender'] ?? '');
        $phone = trim($data['phone'] ?? '');

        $stmt->bind_param(
            "siss",
            $name,
            $age,
            $gender,
            $phone
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    // Update Patient
    public function updatePatient($id, $data) {

        $query = "UPDATE " . $this->table_name . "
                  SET 
                    name = ?,
                    age = ?,
                    gender = ?,
                    phone = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $name = trim($data['name'] ?? '');
        $age = (int) ($data['age'] ?? 0);
        $gender = trim($data['gender'] ?? '');
        $phone = trim($data['phone'] ?? '');

        $stmt->bind_param(
            "sissi",
            $name,
            $age,
            $gender,
            $phone,
            $id
        );

        $stmt->execute();

        $affectedRows = $stmt->affected_rows;

        $stmt->close();

        return $affectedRows > 0;
    }

    // Delete Patient
    public function deletePatient($id) {

        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $affectedRows = $stmt->affected_rows;

        $stmt->close();

        return $affectedRows > 0;
    }
}
?>