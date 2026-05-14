<?php
require_once 'config/db.php';
require_once 'includes/header.php';
require_login();

// Perform all calculations via SQL
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM patients) AS total_patients,
        (SELECT COUNT(*) FROM visits) AS total_visits,
        (SELECT COUNT(*) FROM visits WHERE follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) AS upcoming_followups,
        (SELECT COUNT(*) FROM visits WHERE follow_up_due < CURDATE() AND NOT EXISTS (
            SELECT 1 FROM visits v2 WHERE v2.patient_id = visits.patient_id AND v2.visit_date > visits.follow_up_due
        )) AS overdue_followups
";
$stats = $pdo->query($statsQuery)->fetch(PDO::FETCH_ASSOC);
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5"><i class="bi bi-speedometer2"></i> Dashboard</h1>
        <p class="lead">Welcome to the Healthcare Mini System.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card stat-card primary h-100 border-0">
            <div class="card-body">
                <h5 class="card-title">Total Patients</h5>
                <h2 class="display-4"><?= htmlspecialchars($stats['total_patients'] ?? 0) ?></h2>
                <i class="bi bi-people-fill fs-1 position-absolute top-50 end-0 translate-middle-y me-3 opacity-50"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="patients/list.php" class="text-white text-decoration-none">View All <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success h-100 border-0">
            <div class="card-body">
                <h5 class="card-title">Total Visits</h5>
                <h2 class="display-4"><?= htmlspecialchars($stats['total_visits'] ?? 0) ?></h2>
                <i class="bi bi-calendar-check-fill fs-1 position-absolute top-50 end-0 translate-middle-y me-3 opacity-50"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="visits/list.php" class="text-white text-decoration-none">View All <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info h-100 border-0">
            <div class="card-body">
                <h5 class="card-title">Upcoming Follow-ups</h5>
                <h2 class="display-4"><?= htmlspecialchars($stats['upcoming_followups'] ?? 0) ?></h2>
                <p class="mb-0 small">Next 7 days</p>
                <i class="bi bi-calendar-event fs-1 position-absolute top-50 end-0 translate-middle-y me-3 opacity-50"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="reports/followups.php" class="text-white text-decoration-none">View Report <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card danger h-100 border-0">
            <div class="card-body">
                <h5 class="card-title">Overdue Follow-ups</h5>
                <h2 class="display-4"><?= htmlspecialchars($stats['overdue_followups'] ?? 0) ?></h2>
                <p class="mb-0 small">Missed & overdue</p>
                <i class="bi bi-exclamation-triangle-fill fs-1 position-absolute top-50 end-0 translate-middle-y me-3 opacity-50"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="reports/followups.php" class="text-white text-decoration-none">View Report <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent pt-4 pb-3">
                <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i> Recent Visits</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php
                    $recentVisits = $pdo->query("
                        SELECT v.visit_date, p.name, v.patient_id 
                        FROM visits v 
                        JOIN patients p ON v.patient_id = p.patient_id 
                        ORDER BY v.visit_date DESC LIMIT 5
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($recentVisits as $rv):
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="patients/view.php?id=<?= encode_id($rv['patient_id']) ?>" class="text-decoration-none fw-bold">
                                <?= htmlspecialchars($rv['name']) ?>
                            </a>
                            <br>
                            <small class="text-muted">Visited on: <?= htmlspecialchars($rv['visit_date']) ?></small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent pt-4 pb-3">
                <h5 class="mb-0"><i class="bi bi-person-plus text-success me-2"></i> Newest Patients</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php
                    $recentPatients = $pdo->query("
                        SELECT patient_id, name, join_date, TIMESTAMPDIFF(YEAR, dob, CURDATE()) as age 
                        FROM patients 
                        ORDER BY join_date DESC LIMIT 5
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($recentPatients as $rp):
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="patients/view.php?id=<?= encode_id($rp['patient_id']) ?>" class="text-decoration-none fw-bold">
                                <?= htmlspecialchars($rp['name']) ?>
                            </a> (Age: <?= htmlspecialchars($rp['age']) ?>)
                            <br>
                            <small class="text-muted">Joined: <?= htmlspecialchars($rp['join_date']) ?></small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
