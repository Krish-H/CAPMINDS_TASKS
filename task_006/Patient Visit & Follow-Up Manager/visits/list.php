<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// SQL logic constraints: 
// - Days since visit
// - Whether follow-up is overdue
// - Whether follow-up is upcoming
$query = "
    SELECT 
        v.visit_id,
        v.visit_date,
        v.follow_up_due,
        p.patient_id,
        p.name AS patient_name,
        DATEDIFF(CURDATE(), v.visit_date) AS days_since_visit,
        (v.follow_up_due >= CURDATE()) AS is_upcoming,
        (v.follow_up_due < CURDATE() AND NOT EXISTS (
            SELECT 1 FROM visits v2 WHERE v2.patient_id = v.patient_id AND v2.visit_date > v.follow_up_due
        )) AS is_overdue
    FROM visits v
    JOIN patients p ON v.patient_id = p.patient_id
    ORDER BY v.visit_date DESC
";
$visits = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-calendar-check"></i> All Visits</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="add.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Visit</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Patient</th>
                <th>Days Since Visit</th>
                <th>Follow-up Due</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($visits)): ?>
                <tr><td colspan="6" class="text-center">No visits found.</td></tr>
            <?php else: ?>
                <?php foreach ($visits as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['visit_date']) ?></td>
                    <td>
                        <a href="../patients/view.php?id=<?= $v['patient_id'] ?>" class="text-decoration-none fw-bold">
                            <?= htmlspecialchars($v['patient_name']) ?>
                        </a>
                    </td>
                    <td>
                        <?= $v['days_since_visit'] ?> days ago
                    </td>
                    <td><?= htmlspecialchars($v['follow_up_due']) ?></td>
                    <td>
                        <?php if ($v['is_overdue']): ?>
                            <span class="badge bg-danger">Overdue</span>
                        <?php elseif ($v['is_upcoming']): ?>
                            <span class="badge bg-info text-dark">Upcoming</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Passed / Completed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="patient_visits.php?id=<?= $v['patient_id'] ?>" class="btn btn-sm btn-outline-secondary" title="View Patient History"><i class="bi bi-clock-history"></i> History</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
