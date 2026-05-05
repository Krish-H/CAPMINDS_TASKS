<?php
session_start();

// Protect Dashboard Access
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
$theme = $_SESSION['theme'];


$bs_theme = ($theme === 'dark') ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?php echo htmlspecialchars($bs_theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        /* Warm Theme */
        body.theme-warm {
            background: linear-gradient(135deg, #fceabb 0%, #f8b500 100%);
            color: #4a3f35;
        }
        body.theme-warm .card {
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
            box-shadow: 0 10px 30px rgba(139, 69, 19, 0.15);
        }
        body.theme-warm .btn-danger {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        /* Light/Dark*/
        body:not(.theme-warm)[data-bs-theme="light"] {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        body:not(.theme-warm)[data-bs-theme="dark"] {
            background: linear-gradient(135deg, #1f1c2c 0%, #928dab 100%);
        }
        
        .dashboard-card {
            max-width: 600px;
            width: 100%;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 2rem;
        }
        [data-bs-theme="dark"] .card-header {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .card-body {
            padding: 3rem;
        }

        
        .theme-warm .avatar {
            background: #e67e22;
            box-shadow: 0 5px 15px rgba(230, 126, 34, 0.3);
        }

        .session-details {
            background: rgba(0,0,0,0.03);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        [data-bs-theme="dark"] .session-details {
            background: rgba(255,255,255,0.05);
        }

        .btn {
            border-radius: 0.75rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="<?php echo $theme === 'warm' ? 'theme-warm' : ''; ?>">

    <div class="card dashboard-card">
        <div class="card-header text-center border-0 pt-5 pb-0">
            <h2 class="fw-bold mb-0">Welcome, <?php echo htmlspecialchars(ucfirst($username)); ?>!</h2>
        </div>
        <div class="card-body">
            
            <h5 class="mb-3 text-muted">Session Details</h5>
            <div class="session-details">
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Username:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($username); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Email:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($email); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Active Theme:</div>
                    <div class="col-sm-8"><span class="badge bg-primary text-uppercase"><?php echo htmlspecialchars($theme); ?></span></div>
                </div>
                <div class="row">
                    <div class="col-sm-4 fw-bold">Session ID:</div>
                    <div class="col-sm-8"><small class="text-muted"><?php echo session_id(); ?></small></div>
                </div>
            </div>

            <div class="text-center">
                <a href="logout.php" class="btn btn-danger"> Logout</a>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
