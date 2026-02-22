<?php
require_once __DIR__ . '/../partials/header.php';

$error = '';

if (isLoggedIn()) {
    redirect(BASE_URL . '/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        redirect(BASE_URL . '/dashboard.php');
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<div class="auth-shell" style="max-width: 400px;">
    <div class="glass-card">
        <h2 style="margin-bottom: 1.5rem; text-align: center;">Welcome Back</h2>

        <?php if ($error): ?>
            <div
                style="background: rgba(239, 68, 68, 0.2); color: #f87171; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.9rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Login</button>
        </form>

        <p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: var(--text-dim);">
            Don't have an account? <a href="<?php echo BASE_URL; ?>/auth/register.php"
                style="color: var(--primary); text-decoration: none;">Register here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
