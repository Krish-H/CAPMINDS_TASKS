<?php

class Database {

    private $host = "localhost";
    private $db_name = "hospital_db";
    private $username = "root";
    private $password = "root";

    private $conn = null;

    public function getConnection() {

        if ($this->conn !== null) {
            return $this->conn;
        }

        try {

            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->db_name
            );

            if ($this->conn->connect_error) {
                throw new Exception(
                    "Database connection failed"
                );
            }

            $this->conn->set_charset("utf8mb4");

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Database connection error"
            ]);

            exit();
        }

        return $this->conn;
    }
}
?>