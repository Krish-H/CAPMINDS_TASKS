<?php
require_once __DIR__ . '/auth.php';
$base_url = '/Patient Visit & Follow-Up Manager';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Mini System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="<?= $base_url ?>/assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg mb-5 sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $base_url ?>/index.php">
      <i class="bi bi-heart-pulse-fill fs-4"></i> 
      <span>Health<span style="color: var(--text-main);">Care</span></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Patients</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= $base_url ?>/patients/list.php">List Patients</a></li>
            <li><a class="dropdown-item" href="<?= $base_url ?>/patients/add.php">Add Patient</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Visits</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= $base_url ?>/visits/list.php">All Visits</a></li>
            <li><a class="dropdown-item" href="<?= $base_url ?>/visits/add.php">Add Visit</a></li>
          </ul>
        </li>
        <?php if (is_admin()): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Reports</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= $base_url ?>/reports/summary.php">Summary</a></li>
            <li><a class="dropdown-item" href="<?= $base_url ?>/reports/followups.php">Follow-ups</a></li>
            <li><a class="dropdown-item" href="<?= $base_url ?>/reports/monthly.php">Monthly</a></li>
            <li><a class="dropdown-item" href="<?= $base_url ?>/reports/birthdays.php">Birthdays</a></li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (is_logged_in()): ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-5"></i>
                <span><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Role: <?= ucfirst(htmlspecialchars(get_current_user_role())) ?></h6></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= $base_url ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
