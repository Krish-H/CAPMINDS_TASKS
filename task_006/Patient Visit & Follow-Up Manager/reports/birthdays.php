<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_role('admin');

// SQL logic constraints: 
// 1. Birthdays in next 30 days (Handles Dec->Jan boundary via DATE_ADD logic)
$upcomingBirthdaysQuery = "
    SELECT 
        patient_id, name, phone, dob,
        DATE_ADD(dob, INTERVAL YEAR(CURDATE()) - YEAR(dob) + IF(DATE_FORMAT(CURDATE(), '%m%d') > DATE_FORMAT(dob, '%m%d'), 1, 0) YEAR) AS next_birthday,
        TIMESTAMPDIFF(YEAR, dob, CURDATE()) + IF(DATE_FORMAT(CURDATE(), '%m%d') > DATE_FORMAT(dob, '%m%d'), 1, 0) AS turning_age
    FROM patients
    HAVING next_birthday BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY next_birthday ASC
";
$upcomingBirthdays = $pdo->query($upcomingBirthdaysQuery)->fetchAll(PDO::FETCH_ASSOC);

// 2. Patients turning exactly 40, 50, or 60 THIS year
$milestoneQuery = "
    SELECT 
        patient_id, name, dob,
        (YEAR(CURDATE()) - YEAR(dob)) AS turning_age_this_year
    FROM patients
    WHERE (YEAR(CURDATE()) - YEAR(dob)) IN (40, 50, 60)
    ORDER BY turning_age_this_year ASC, dob ASC
";
$milestoneBirthdays = $pdo->query($milestoneQuery)->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-gift"></i> Birthday Reports</h2>
        <p class="text-muted">Tracking upcoming birthdays and milestones using strict SQL date logic.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-primary">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="bi bi-calendar2-event"></i> Birthdays in Next 30 Days
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($upcomingBirthdays)): ?>
                        <li class="list-group-item text-center">No birthdays in the next 30 days.</li>
                    <?php else: ?>
                        <?php foreach ($upcomingBirthdays as $ub): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="../patients/view.php?id=<?= encode_id($ub['patient_id']) ?>" class="text-decoration-none fw-bold">
                                    <?= htmlspecialchars($ub['name']) ?>
                                </a>
                                <br>
                                <small class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($ub['phone']) ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark">Turning <?= $ub['turning_age'] ?></span>
                                <br>
                                <small><?= date('M j, Y', strtotime($ub['next_birthday'])) ?></small>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100 border-success">
            <div class="card-header bg-success text-white fw-bold">
                <i class="bi bi-star"></i> Milestone Birthdays This Year (Turning 40, 50, 60)
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($milestoneBirthdays)): ?>
                        <li class="list-group-item text-center">No patients hitting 40, 50, or 60 this year.</li>
                    <?php else: ?>
                        <?php foreach ($milestoneBirthdays as $mb): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="../patients/view.php?id=<?= encode_id($mb['patient_id']) ?>" class="text-decoration-none fw-bold">
                                    <?= htmlspecialchars($mb['name']) ?>
                                </a>
                                <br>
                                <small class="text-muted">Born: <?= htmlspecialchars($mb['dob']) ?></small>
                            </div>
                            <div>
                                <span class="badge bg-success fs-6">Turning <?= $mb['turning_age_this_year'] ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
