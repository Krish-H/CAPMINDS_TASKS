<?php
session_start();

// Gets theme from cookie
$theme = isset($_COOKIE['user_theme']) ? $_COOKIE['user_theme'] : 'light';

$bs_theme = ($theme === 'dark') ? 'dark' : 'light';

// Remembers username
$saved_username = isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : '';

// Shows login errors
$error_msg = '';
if (isset($_SESSION['error'])) {
    $error_msg = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?php echo htmlspecialchars($bs_theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        body.theme-warm {
            background: linear-gradient(135deg, #fceabb 0%, #f8b500 100%);
            color: #4a3f35;
        }
        body.theme-warm .card {
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            box-shadow: 0 10px 30px rgba(139, 69, 19, 0.15);
        }
        body.theme-warm .form-control {
            border-color: #f0c27b;
            background-color: #fffaf0;
        }
        body.theme-warm .btn-primary {
            background-color: #e67e22;
            border-color: #e67e22;
        }
        body.theme-warm .btn-primary:hover {
            background-color: #d35400;
            border-color: #d35400;
        }

        body:not(.theme-warm)[data-bs-theme="light"] {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        body:not(.theme-warm)[data-bs-theme="dark"] {
            background: linear-gradient(135deg, #1f1c2c 0%, #928dab 100%);
        }
        
        .login-card {
            max-width: 420px;
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

        .card-body {
            padding: 3rem;
        }

        .form-control {
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            transform: translateY(-2px);
        }

        .btn-primary {
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }


    </style>
</head>
<body class="<?php echo $theme === 'warm' ? 'theme-warm' : ''; ?>">

    <div class="card login-card">
        <div class="card-body">

            <h3 class="text-center mb-4 fw-bold">Login</h3>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" id="username" class="form-control" name="username" value="<?php echo htmlspecialchars($saved_username); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" name="email" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" id="password" class="form-control" name="password" required>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" <?php echo !empty($saved_username) ? 'checked' : ''; ?>>
                    <label class="form-check-label">Remember me for 7 days</label>
                </div>
                
                <button class="btn btn-primary w-100">Login</button>
            </form>

            
            <div class="mt-4 text-center text-muted small">
                <p class="mb-1">Demo Accounts:</p>
                <code onclick="fillUser('admin')" style="cursor:pointer">admin</code> (Dark) |
                <code onclick="fillUser('user2')" style="cursor:pointer">user2</code> (Warm) |
                <code onclick="fillUser('user3')" style="cursor:pointer">user3</code> (Light)
            </div>
        </div>
    </div>

    <!-- ✅ fill User by click -->
    <script>
    function fillUser(user) {
        const data = {
            admin: {username:"admin", email:"admin@gmail.com", password:"Admin@123"},
            user2: {username:"user2", email:"user2@gmail.com", password:"User2@123"},
            user3: {username:"user3", email:"user3@gmail.com", password:"User3@123"}
        };

        if (data[user]) {
            document.getElementById("username").value = data[user].username;
            document.getElementById("email").value = data[user].email;
            document.getElementById("password").value = data[user].password;
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>