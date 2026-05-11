<?php
require_once '../config/db.php';

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        // Check email uniqueness
        $stmt = $conn->prepare("SELECT id FROM patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered.";
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
        $stmt = $conn->prepare("INSERT INTO patients (patient_name, email, phone, age, gender, diagnosis) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $name, $email, $phone, $age, $gender, $diagnosis);
        if ($stmt->execute()) {
            $success = "Patient successfully added.";
            // Reset fields
            $name = $email = $phone = $age = $gender = $diagnosis = '';
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="page-title">Add New Patient</h2>

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

        <form action="create.php" method="POST" class="form-container">
            <div class="mb-3">
                <label for="patient_name" class="form-label fw-bold">Patient Name</label>
                <input type="text" class="form-control" id="patient_name" name="patient_name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label fw-bold">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label fw-bold">Age</label>
                <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($age ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label fw-bold">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo (isset($gender) && $gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($gender) && $gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($gender) && $gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="diagnosis" class="form-label fw-bold">Diagnosis</label>
                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required><?php echo htmlspecialchars($diagnosis ?? ''); ?></textarea>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary px-4">Save Patient</button>
                <a href="list.php" class="btn btn-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
