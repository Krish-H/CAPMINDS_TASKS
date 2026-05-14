<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_role('admin');

// SQL logic constraints: Full Summary Report
$query = "
    SELECT 
        p.patient_id,
        p.name,
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age,
        (SELECT COUNT(*) FROM visits WHERE visits.patient_id = p.patient_id) AS total_visits,
        (SELECT MAX(visit_date) FROM visits WHERE visits.patient_id = p.patient_id) AS last_visit_date,
        (SELECT DATEDIFF(CURDATE(), MAX(visit_date)) FROM visits WHERE visits.patient_id = p.patient_id) AS days_since_last_visit,
        (SELECT MAX(follow_up_due) FROM visits WHERE visits.patient_id = p.patient_id AND follow_up_due >= CURDATE()) AS next_follow_up
    FROM patients p
    ORDER BY p.name ASC
";
$summaries = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-file-earmark-medical"></i> Full Summary Report</h2>
        <p class="text-muted">Comprehensive view of all patients, their visit counts, and follow-up schedules. (Powered exclusively by SQL)</p>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="">
            <tr>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Total Visits</th>
                <th>Last Visit Date</th>
                <th>Days Since Last Visit</th>
                <th>Next Follow-up</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($summaries)): ?>
                <tr><td colspan="6" class="text-center">No patient data found.</td></tr>
            <?php else: ?>
                <?php foreach ($summaries as $s): ?>
                <tr>
                    <td>
                        <a href="../patients/view.php?id=<?= $s['patient_id'] ?>" class="text-decoration-none fw-bold text-dark">
                            <?= htmlspecialchars($s['name']) ?>
                        </a>
                    </td>
                    <td><?= $s['age'] ?></td>
                    <td><span class="badge bg-secondary"><?= $s['total_visits'] ?></span></td>
                    <td>
                        <?= $s['last_visit_date'] ? htmlspecialchars($s['last_visit_date']) : '<span class="text-muted">Never</span>' ?>
                    </td>
                    <td>
                        <?php if ($s['days_since_last_visit'] !== null): ?>
                            <?= $s['days_since_last_visit'] ?> days
                            <?php if ($s['days_since_last_visit'] > 180): ?>
                                <span class="badge bg-danger ms-1" title="Inactive for 180+ days">Inactive</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">No visits</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($s['next_follow_up']): ?>
                            <span class="badge bg-info text-dark"><?= htmlspecialchars($s['next_follow_up']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
