<?php
require_once 'config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

$MAX_DAILY_APPOINTMENTS = 50;

// Helper function for sending JSON response
function sendResponse($code, $message, $data = null) {
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    exit;
}

// CSRF Validation for state-changing methods
if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    $token = '';
    if ($method === 'DELETE' && isset($_GET['csrf_token'])) {
        $token = $_GET['csrf_token'];
    } elseif (isset($input['csrf_token'])) {
        $token = $input['csrf_token'];
    }
    
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        sendResponse(403, 'Invalid CSRF token');
    }
}

switch ($method) {
    case 'GET':
        if (isset($_GET['csrf'])) {
            // Endpoint to get CSRF token
            sendResponse(200, 'CSRF token', ['csrf_token' => $_SESSION['csrf_token']]);
        }
        
        $stmt = $conn->prepare("SELECT * FROM appointments ORDER BY appointment_date ASC, appointment_time ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $appointments = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse(200, 'Appointments fetched', $appointments);
        break;

    case 'POST':
        $name = trim($input['patient_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $mobile = trim($input['mobile'] ?? '');
        $doctor = trim($input['doctor_name'] ?? '');
        $date = trim($input['appointment_date'] ?? '');
        $time = trim($input['appointment_time'] ?? '');

        // Validation
        if (!$name || !$email || !$mobile || !$doctor || !$date || !$time) {
            sendResponse(400, 'All fields are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(400, 'Invalid email format');
        }

        if (strlen($mobile) < 10 || strlen($mobile) > 15) {
            sendResponse(400, 'Mobile number must be between 10 and 15 digits');
        }

        if (strtotime($date) < strtotime('today')) {
            sendResponse(400, 'Appointment date cannot be in the past');
        }
        
        // Time slot validation (09:00 - 17:00)
        $hour = (int) date('H', strtotime($time));
        if ($hour < 9 || $hour >= 17) {
            sendResponse(400, 'Please select a time slot between 09:00 AM and 05:00 PM');
        }

        // Daily Limit Validation
        $limitStmt = $conn->prepare("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = ?");
        $limitStmt->bind_param("s", $date);
        $limitStmt->execute();
        $limitResult = $limitStmt->get_result()->fetch_assoc();
        if ($limitResult['count'] >= $MAX_DAILY_APPOINTMENTS) {
            sendResponse(400, 'Daily appointment limit reached. Please select another date.');
        }

        // Prevent Double Booking
        $checkStmt = $conn->prepare("SELECT id FROM appointments WHERE doctor_name = ? AND appointment_date = ? AND appointment_time = ?");
        $checkStmt->bind_param("sss", $doctor, $date, $time);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            sendResponse(400, 'Double booking error: This slot is already taken for the selected doctor.');
        }

        // Insert
        $stmt = $conn->prepare("INSERT INTO appointments (patient_name, email, mobile, doctor_name, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssssss", $name, $email, $mobile, $doctor, $date, $time);
        
        if ($stmt->execute()) {
            sendResponse(201, 'Appointment created successfully');
        } else {
            sendResponse(500, 'Database error');
        }
        break;

    case 'PUT':
        if (isset($input['status_only']) && $input['status_only'] == true) {
            // Update Status
            $id = (int)($input['id'] ?? 0);
            $status = trim($input['status'] ?? '');
            
            if (!$id || !in_array($status, ['Pending', 'Confirmed', 'Cancelled'])) {
                sendResponse(400, 'Invalid input for status update');
            }
            
            $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                sendResponse(200, 'Status updated successfully');
            } else {
                sendResponse(500, 'Database error');
            }
        } else {
            // Update full appointment
            $id = (int)($input['id'] ?? 0);
            $name = trim($input['patient_name'] ?? '');
            $email = trim($input['email'] ?? '');
            $mobile = trim($input['mobile'] ?? '');
            $doctor = trim($input['doctor_name'] ?? '');
            $date = trim($input['appointment_date'] ?? '');
            $time = trim($input['appointment_time'] ?? '');

            if (!$id || !$name || !$email || !$mobile || !$doctor || !$date || !$time) {
                sendResponse(400, 'All fields are required');
            }
            
            if (strtotime($date) < strtotime('today')) {
                sendResponse(400, 'Appointment date cannot be in the past');
            }

            // Prevent Double Booking (ignoring current appointment)
            $checkStmt = $conn->prepare("SELECT id FROM appointments WHERE doctor_name = ? AND appointment_date = ? AND appointment_time = ? AND id != ?");
            $checkStmt->bind_param("sssi", $doctor, $date, $time, $id);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                sendResponse(400, 'Double booking error: This slot is already taken for the selected doctor.');
            }

            $stmt = $conn->prepare("UPDATE appointments SET patient_name = ?, email = ?, mobile = ?, doctor_name = ?, appointment_date = ?, appointment_time = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $name, $email, $mobile, $doctor, $date, $time, $id);
            
            if ($stmt->execute()) {
                sendResponse(200, 'Appointment updated successfully');
            } else {
                sendResponse(500, 'Database error');
            }
        }
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            sendResponse(400, 'Appointment ID required');
        }
        
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                sendResponse(200, 'Appointment deleted successfully');
            } else {
                sendResponse(404, 'Appointment not found');
            }
        } else {
            sendResponse(500, 'Database error');
        }
        break;

    default:
        sendResponse(405, 'Method not allowed');
}
?>
