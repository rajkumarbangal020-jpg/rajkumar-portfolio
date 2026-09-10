<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Security token invalid or expired. Please refresh and try again.';
    }

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($error_message)) {
        if (empty($username) || empty($password)) {
            $error_message = "Please enter both username and password.";
        } else {
            $db = get_db_connection();
            if (!$db) {
                $error_message = "Database unavailable. Configure the database and create the first admin account.";
            } else {
                try {
                    $stmt = $db->prepare("SELECT * FROM admins WHERE username = :login OR email = :login LIMIT 1");
                    $stmt->execute([':login' => $username]);
                    $admin = $stmt->fetch();

                    if ($admin && password_verify($password, $admin['password_hash'])) {
                        session_regenerate_id(true);
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_username'] = $admin['username'];
                        header("Location: dashboard.php");
                        exit;
                    }
                    $error_message = "Invalid username or password!";
                } catch (Exception $e) {
                    $error_message = "Unable to sign in right now.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Rajkumar Bangal Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
</head>
<body class="admin-body d-flex align-items-center justify-content-center min-vh-100 p-3">
<div class="card admin-card p-4 shadow-lg" style="max-width: 420px; width: 100%;">
    <div class="text-center mb-4">
        <i class="fas fa-user-shield text-info display-4 mb-2"></i>
        <h4 class="font-weight-bold admin-text-primary mb-1">Admin Authentication</h4>
        <p class="admin-text-muted small">Rajkumar Bangal Portfolio Management</p>
    </div>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger py-2 text-center small mb-3"><i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
        <div class="mb-3">
            <label class="form-label small font-weight-bold">Username</label>
            <div class="input-group"><span class="input-group-text"><i class="fas fa-user"></i></span><input type="text" name="username" class="form-control" placeholder="Username or email" autocomplete="username" required></div>
        </div>
        <div class="mb-4">
            <label class="form-label small font-weight-bold">Password</label>
            <div class="input-group"><span class="input-group-text"><i class="fas fa-lock"></i></span><input type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="current-password" required></div>
        </div>
        <button type="submit" class="btn btn-info text-dark font-weight-bold w-100 py-2"><i class="fas fa-sign-in-alt me-1"></i> Sign In to Dashboard</button>
    </form>
    <div class="text-center mt-4 border-top border-secondary pt-3">
        <a href="<?php echo BASE_URL; ?>" class="admin-text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Return to Main Website</a>
    </div>
</div>
</body>
</html>
