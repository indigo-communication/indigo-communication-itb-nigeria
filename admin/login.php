<?php
session_start();

// If already logged in, go straight to dashboard
if (!empty($_SESSION['itbng_auth'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $user   = trim($_POST['username'] ?? '');
        $pass   = $_POST['password'] ?? '';

        $valid_user = 'info@indigo-lb.com';
        $valid_hash = '$2y$10$4YeUDVqZ5jwT6FJ5hu.QTuoFv3LlZnnD8SbOYXGUpLv8sdozzFHXC';

        if (hash_equals($valid_user, $user) && password_verify($pass, $valid_hash)) {
            session_regenerate_id(true);
            $_SESSION['itbng_auth'] = true;
            $_SESSION['itbng_user'] = $user;
            header('Location: index.php');
            exit;
        } else {
            // Constant-time delay to slow brute force
            usleep(500000);
            $error = 'Invalid username or password.';
        }
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>itbng.com — Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f5f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,.10);
        }
        .login-logo {
            font-family: Montserrat, Arial, sans-serif;
            font-weight: 800;
            font-size: 2rem;
            letter-spacing: .08em;
            color: #6B4DE6;
        }
        .login-logo span {
            font-weight: 400;
            font-size: 1rem;
            color: #888;
            letter-spacing: .05em;
        }
        .btn-login {
            background: #6B4DE6;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: .04em;
        }
        .btn-login:hover { background: #5a3dcc; }
        .form-control:focus { border-color: #6B4DE6; box-shadow: 0 0 0 .2rem rgba(107,77,230,.18); }
        .alert-danger { border-radius: 8px; font-size: .9rem; }
    </style>
</head>
<body>
    <div class="login-card card p-5">
        <div class="text-center mb-4">
            <div class="login-logo">ITB<span>NG.COM</span></div>
            <p class="text-muted mt-1 mb-0 small">Traffic Admin Dashboard</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small text-muted text-uppercase ls-1">Email</label>
                <input type="email" class="form-control" id="username" name="username"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small text-muted text-uppercase ls-1">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-login btn-primary w-100 py-2 text-white">
                Sign In
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            itbng.com &nbsp;·&nbsp; Restricted Access
        </p>
    </div>
</body>
</html>
