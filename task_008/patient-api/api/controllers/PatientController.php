<?php
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../helpers/Response.php';

class PatientController {
    private $patient;

    public function __construct($db) {
        $this->patient = new Patient($db);
    }

    public function processRequest($method, $id) {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getPatient($id);
                } else {
                    $this->getAllPatients();
                }
                break;
            case 'POST':
                $this->createPatient();
                break;
            case 'PUT':
                if ($id) {
                    $this->updatePatient($id);
                } else {
                    Response::send(400, false, "Patient ID is required");
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deletePatient($id);
                } else {
                    Response::send(400, false, "Patient ID is required");
                }
                break;
            default:
                Response::send(405, false, "Method not allowed");
                break;
        }
    }

    private function getAllPatients() {
        $result = $this->patient->getAllPatients();
        Response::send(200, true, "Patients retrieved successfully", $result);
    }

    private function getPatient($id) {
        $result = $this->patient->getPatientById($id);
        if ($result) {
            Response::send(200, true, "Patient retrieved successfully", $result);
        } else {
            Response::send(404, false, "Patient not found");
        }
    }

    private function createPatient() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['name']) && isset($data['age']) && isset($data['gender']) && isset($data['phone'])) {
            if ($this->patient->createPatient($data)) {
                Response::send(201, true, "Patient created successfully");
            } else {
                Response::send(500, false, "Failed to create patient");
            }
        } else {
            Response::send(400, false, "Incomplete data");
        }
    }

    private function updatePatient($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $existingPatient = $this->patient->getPatientById($id);
        
        if (!$existingPatient) {
            Response::send(404, false, "Patient not found");
            return;
        }

        $updateData = [
            'name' => isset($data['name']) ? $data['name'] : $existingPatient['name'],
            'age' => isset($data['age']) ? $data['age'] : $existingPatient['age'],
            'gender' => isset($data['gender']) ? $data['gender'] : $existingPatient['gender'],
            'phone' => isset($data['phone']) ? $data['phone'] : $existingPatient['phone']
        ];

        if ($this->patient->updatePatient($id, $updateData)) {
            Response::send(200, true, "Patient updated successfully");
        } else {
            Response::send(500, false, "Failed to update patient");
        }
    }

    private function deletePatient($id) {
        $existingPatient = $this->patient->getPatientById($id);
        if (!$existingPatient) {
            Response::send(404, false, "Patient not found");
            return;
        }

        if ($this->patient->deletePatient($id)) {
            Response::send(200, true, "Patient deleted successfully");
        } else {
            Response::send(500, false, "Failed to delete patient");
        }
    }
}
?>
