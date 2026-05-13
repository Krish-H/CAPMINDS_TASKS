<?php
require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$patient_id = (int)$_GET['id'];

// Fetch patient strictly with SQL calculations
$query = "
    SELECT 
        patients.*,
        TIMESTAMPDIFF(YEAR, dob, CURDATE()) AS age_years,
        TIMESTAMPDIFF(MONTH, dob, CURDATE()) % 12 AS age_months,
        (SELECT DATEDIFF(CURDATE(), MAX(visit_date)) FROM visits WHERE visits.patient_id = patients.patient_id) AS days_since_last_visit,
        (SELECT MIN(follow_up_due) FROM visits WHERE visits.patient_id = patients.patient_id AND follow_up_due >= CURDATE()) AS next_follow_up,
        (SELECT COUNT(*) FROM visits WHERE visits.patient_id = patients.patient_id AND follow_up_due < CURDATE() AND NOT EXISTS (
            SELECT 1 FROM visits v2 WHERE v2.patient_id = visits.patient_id AND v2.visit_date > visits.follow_up_due
        )) > 0 AS has_overdue_followup
    FROM patients
    WHERE patient_id = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found.";
    exit;
}

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-person-badge"></i> Patient Profile</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="edit.php?id=<?= $patient_id ?>" class="btn btn-secondary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="../visits/add.php?patient_id=<?= $patient_id ?>" class="btn btn-primary"><i class="bi bi-calendar-plus"></i> New Visit</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold">
                Personal Information
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th width="30%">Name:</th><td><?= htmlspecialchars($patient['name']) ?></td></tr>
                    <tr><th>Age:</th><td><?= $patient['age_years'] ?> years, <?= $patient['age_months'] ?> months</td></tr>
                    <tr><th>DOB:</th><td><?= htmlspecialchars($patient['dob']) ?></td></tr>
                    <tr><th>Join Date:</th><td><?= htmlspecialchars($patient['join_date']) ?></td></tr>
                    <tr><th>Phone:</th><td><?= htmlspecialchars($patient['phone']) ?></td></tr>
                    <tr><th>Address:</th><td><?= nl2br(htmlspecialchars($patient['address'])) ?></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold">
                Activity Summary (SQL Calculated)
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">Days Since Last Visit:</th>
                        <td>
                            <?php if ($patient['days_since_last_visit'] === null): ?>
                                <span class="text-muted">No visits yet</span>
                            <?php else: ?>
                                <?= htmlspecialchars($patient['days_since_last_visit']) ?> days
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Next Follow-up:</th>
                        <td>
                            <?php if ($patient['next_follow_up']): ?>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($patient['next_follow_up']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">None Scheduled</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Overdue Follow-ups:</th>
                        <td>
                            <?php if ($patient['has_overdue_followup']): ?>
                                <span class="badge bg-danger">Yes - Overdue!</span>
                            <?php else: ?>
                                <span class="badge bg-success">No</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h4>Recent Visits</h4>
        <?php
        // Basic list of visits for this patient
        $vStmt = $pdo->prepare("SELECT * FROM visits WHERE patient_id = ? ORDER BY visit_date DESC LIMIT 5");
        $vStmt->execute([$patient_id]);
        $visits = $vStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Consultation Fee</th>
                        <th>Lab Fee</th>
                        <th>Follow-up Due</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($visits)): ?>
                        <tr><td colspan="4" class="text-center">No visits recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($visits as $v): ?>
                        <tr>
                            <td><?= htmlspecialchars($v['visit_date']) ?></td>
                            <td>$<?= htmlspecialchars($v['consultation_fee']) ?></td>
                            <td>$<?= htmlspecialchars($v['lab_fee']) ?></td>
                            <td><?= htmlspecialchars($v['follow_up_due']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-end">
            <a href="../visits/patient_visits.php?id=<?= $patient_id ?>" class="btn btn-sm btn-outline-primary">View Full Visit History</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
