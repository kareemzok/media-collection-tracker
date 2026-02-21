<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediaTracker - Your Personal Collection</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>

<body>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>/" class="brand">MediaTracker</a>
        <div class="nav-links">
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/media-list.php">Collection</a>
                <a href="<?php echo BASE_URL; ?>/profile.php">Profile</a>
                <?php if (isAdmin()): ?>
                    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" style="color: #fbbf24;">Admin</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="btn btn-primary"
                    style="padding: 0.4rem 0.8rem;">Logout</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/auth/login.php">Login</a>
                <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-primary">Join Now</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container">