<?php
require_once '../config/db.php';

// SQL logic constraints: Monthly Reports
// Visits per month & Patients joined per month
$query = "
    SELECT 
        DATE_FORMAT(date_col, '%Y-%m') AS month_year,
        SUM(visit_count) as total_visits,
        SUM(join_count) as total_joins
    FROM (
        SELECT visit_date AS date_col, 1 AS visit_count, 0 AS join_count FROM visits
        UNION ALL
        SELECT join_date AS date_col, 0 AS visit_count, 1 AS join_count FROM patients
    ) as combined
    GROUP BY DATE_FORMAT(date_col, '%Y-%m')
    ORDER BY month_year ASC
    LIMIT 12
";
$monthlyData = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js
$labels = [];
$visits = [];
$joins = [];

foreach ($monthlyData as $row) {
    $labels[] = $row['month_year'];
    $visits[] = $row['total_visits'];
    $joins[] = $row['total_joins'];
}

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-bar-chart"></i> Monthly Analytics</h2>
        <p class="text-muted">Displays patient registrations and visits grouped by month (Last 12 active months).</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Month / Year</th>
                        <th>New Patients Joined</th>
                        <th>Total Visits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($monthlyData)): ?>
                        <tr><td colspan="3" class="text-center">No data available.</td></tr>
                    <?php else: ?>
                        <!-- Displaying in reverse order (newest first) for the table -->
                        <?php foreach (array_reverse($monthlyData) as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['month_year']) ?></strong></td>
                            <td><span class="badge bg-success"><?= $row['total_joins'] ?></span></td>
                            <td><span class="badge bg-primary"><?= $row['total_visits'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Injection -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: 'Visits',
                    data: <?= json_encode($visits) ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.5)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                },
                {
                    label: 'New Patients',
                    data: <?= json_encode($joins) ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.5)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
