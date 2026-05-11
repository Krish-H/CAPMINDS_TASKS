<?php
require_once '../config/db.php';

$errors = [];
$success = '';

if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    header("Location: list.php");
    exit;
}

$id = $_REQUEST['id'];

// Fetch existing patient data
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list.php");
    exit;
}

$patient = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['patient_name'])) {
    $name = trim($_POST['patient_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $diagnosis = trim($_POST['diagnosis']);

    // Validation
    if (empty($name)) {
        $errors[] = "Patient Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    } else {
        // Check email uniqueness, excluding current patient
        $stmt = $conn->prepare("SELECT id FROM patients WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered by another patient.";
        }
        $stmt->close();
    }
    if (empty($phone) || !preg_match("/^[0-9+\-\s()]+$/", $phone)) {
        $errors[] = "Valid phone number is required (numbers, spaces, and + - ( ) allowed).";
    }
    if (empty($age) || !is_numeric($age) || $age <= 0) {
        $errors[] = "Valid age is required.";
    }
    if (empty($gender)) {
        $errors[] = "Gender is required.";
    }
    if (empty($diagnosis)) {
        $errors[] = "Diagnosis is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE patients SET patient_name=?, email=?, phone=?, age=?, gender=?, diagnosis=? WHERE id=?");
        $stmt->bind_param("sssissi", $name, $email, $phone, $age, $gender, $diagnosis, $id);
        if ($stmt->execute()) {
            $success = "Patient successfully updated.";
            // Update current patient array for form re-population
            $patient['patient_name'] = $name;
            $patient['email'] = $email;
            $patient['phone'] = $phone;
            $patient['age'] = $age;
            $patient['gender'] = $gender;
            $patient['diagnosis'] = $diagnosis;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="page-title">Edit Patient</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="edit.php" method="POST" class="form-container">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <div class="mb-3">
                <label for="patient_name" class="form-label fw-bold">Patient Name</label>
                <input type="text" class="form-control" id="patient_name" name="patient_name" value="<?php echo htmlspecialchars($patient['patient_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label fw-bold">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label fw-bold">Age</label>
                <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($patient['age']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label fw-bold">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo ($patient['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($patient['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($patient['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="diagnosis" class="form-label fw-bold">Diagnosis</label>
                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required><?php echo htmlspecialchars($patient['diagnosis']); ?></textarea>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary px-4">Update Patient</button>
                <a href="list.php" class="btn btn-secondary px-4">Back to List</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
