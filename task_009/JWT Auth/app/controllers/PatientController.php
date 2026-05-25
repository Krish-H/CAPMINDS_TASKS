<?php

class PatientController
{
    private $patientModel;

    public function __construct()
    {
        $this->patientModel = new Patient();
    }

    public function index()
    {
        $patients = $this->patientModel->getAll();
        Response::json(['data' => $patients], 200);
    }

    public function store()
    {
        $body = $_REQUEST['body'] ?? [];
        
        $requiredFields = ['name', 'age', 'gender', 'phone', 'address'];
        $data = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($body[$field]) || trim($body[$field]) === '') {
                Response::json(['error' => ucfirst($field) . ' is required'], 400);
            }
            $data[$field] = trim($body[$field]);
        }

        $patientId = $this->patientModel->create($data);

        if ($patientId) {
            $data['id'] = $patientId;
            Response::json(['message' => 'Patient created successfully', 'data' => $data], 201);
        } else {
            Response::json(['error' => 'Failed to create patient'], 500);
        }
    }

    public function update($id)
    {
        $patient = $this->patientModel->findById($id);
        if (!$patient) {
            Response::json(['error' => 'Patient not found'], 404);
        }

        $body = $_REQUEST['body'] ?? [];
        $allowedFields = ['name', 'age', 'gender', 'phone', 'address'];
        $data = [];
        
        foreach ($allowedFields as $field) {
            if (isset($body[$field])) {
                $data[$field] = trim($body[$field]);
            }
        }

        if (empty($data)) {
            Response::json(['error' => 'No fields to update'], 400);
        }

        $updated = $this->patientModel->update($id, $data);

        if ($updated) {
            Response::json(['message' => 'Patient updated successfully'], 200);
        } else {
            Response::json(['error' => 'Failed to update patient'], 500);
        }
    }

    public function destroy($id)
    {
        $patient = $this->patientModel->findById($id);
        if (!$patient) {
            Response::json(['error' => 'Patient not found'], 404);
        }

        $deleted = $this->patientModel->delete($id);

        if ($deleted) {
            Response::json(['message' => 'Patient deleted successfully'], 200);
        } else {
            Response::json(['error' => 'Failed to delete patient'], 500);
        }
    }
}
