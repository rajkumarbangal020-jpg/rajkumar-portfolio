<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = get_db_connection();
$message = '';
$error = '';
$adminExists = false;

if ($db) {
    try {
        $adminExists = (int)$db->query("SELECT COUNT(*) FROM admins")->fetchColumn() > 0;
    } catch (Exception $e) {
        $error = 'Unable to read the admins table. Import database/portfolio.sql first.';
    }
} else {
    $error = 'Database connection failed. Configure the PORTFOLIO_DB_* environment variables or local MySQL settings first.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$adminExists && !$error) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid or expired.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
            $error = 'Username must be 3–50 characters and use only letters, numbers, dot, dash or underscore.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Enter a valid email address.';
        } elseif (strlen($password) < 12) {
            $error = 'Password must be at least 12 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO admins (username, email, password_hash) VALUES (:username, :email, :password_hash)");
                $stmt->execute([':username'=>$username, ':email'=>$email, ':password_hash'=>password_hash($password, PASSWORD_DEFAULT)]);
                $adminExists = true;
                $message = 'Admin account created. For security, delete or disable setup_admin.php after setup.';
            } catch (Exception $e) {
                $error = 'Could not create the admin account. The username or email may already exist.';
            }
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Initial Admin Setup | Rajkumar Bangal Portfolio</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css"></head>
<body class="admin-body d-flex align-items-center justify-content-center min-vh-100 p-3"><div class="card admin-card p-4 shadow-lg" style="max-width: 520px; width:100%;"><h3 class="admin-text-primary mb-2">Initial Admin Setup</h3><p class="admin-text-muted">Create the first admin account once, then remove or disable this file on production.</p>
<?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?><?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($adminExists): ?><div class="alert alert-info">An admin account already exists. Setup is locked.</div><a class="btn btn-info" href="login.php">Go to Admin Login</a>
<?php elseif (!$error || $db): ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>"><div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" autocomplete="username" required></div><div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" autocomplete="email" required></div><div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" minlength="12" autocomplete="new-password" required></div><div class="mb-3"><label class="form-label">Confirm Password</label><input class="form-control" type="password" name="confirm_password" minlength="12" autocomplete="new-password" required></div><button class="btn btn-info w-100" type="submit">Create Admin</button></form><?php endif; ?></div></body></html>
