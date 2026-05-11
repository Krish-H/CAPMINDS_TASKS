<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Management System</title>

    <!-- Bootstrap CSS -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Custom CSS -->

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<!-- Navbar -->

<nav class="navbar navbar-expand-lg navbar-dark custom-navbar sticky-top">

    <div class="container">

        <!-- Logo -->

        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="../index.php">

            <div class="logo-box">

                <i class="bi bi-hospital-fill"></i>

            </div>

            <div>

                <div class="brand-title">
                    Hospital Management
                </div>

                <small class="brand-subtitle">
                    Patient Care System
                </small>

            </div>

        </a>

        <!-- Mobile Toggle -->

        <button
            class="navbar-toggler border-0 shadow-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >

            <span class="navbar-toggler-icon"></span>

        </button>

        <!-- Menu -->

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3 mt-3 mt-lg-0">

                <li class="nav-item">

                    <a class="nav-link custom-link active" href="../patients/list.php">

                        <i class="bi bi-people-fill me-1"></i>

                        Patients

                    </a>

                </li>

                <li class="nav-item">

                    <a class="btn btn-light add-btn px-4 py-2 fw-semibold"
                       href="../patients/create.php">

                        <i class="bi bi-plus-circle-fill me-2"></i>

                        Add Patient

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>

<!-- Page Container -->

<div class="container mt-5 mb-5">