<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediaTracker - Your Personal Collection Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .hero {
            height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            line-height: 1;
            background: linear-gradient(to right, #6366f1, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-dim);
            max-width: 600px;
            margin-bottom: 2.5rem;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(99, 102, 241, 0.15);
            filter: blur(80px);
            border-radius: 50%;
            top: 20%;
            left: 20%;
            z-index: -1;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(168, 85, 247, 0.1);
            filter: blur(100px);
            border-radius: 50%;
            bottom: 10%;
            right: 15%;
            z-index: -1;
        }

        @media (max-width: 900px) {
            .hero {
                height: auto;
                min-height: 70vh;
                padding: 3rem 0 2rem;
            }

            .hero h1 {
                font-size: clamp(2.2rem, 9vw, 3.2rem);
            }

            .hero p {
                font-size: 1.05rem;
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 640px) {
            .hero::before,
            .hero::after {
                display: none;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>/" class="brand">MediaTracker</a>
        <button class="nav-toggle" type="button" aria-label="Open menu" aria-controls="landing-nav" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="nav-toggle-icon" aria-hidden="true">
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
            </span>
        </button>
        <div class="nav-links" id="landing-nav">
            <a href="<?php echo BASE_URL; ?>/auth/login.php">Login</a>
            <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-primary">Get Started</a>
        </div>
    </nav>

    <div class="container">
        <section class="hero">
            <h1>Track Every Movie, <br> Game, and Album.</h1>
            <p>The premium personal media vault for collectors. Organize your collection, manage your wishlist, and
                share your taste with the world.</p>
            <div class="hero-cta">
                <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-primary"
                    style="padding: 1rem 2.5rem; font-size: 1.1rem;">Start Collecting</a>
                <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-glass"
                    style="padding: 1rem 2.5rem; font-size: 1.1rem;">Sign
                    In</a>
            </div>
        </section>

        <div class="grid" style="margin-bottom: 6rem;">
            <div class="glass-card" style="text-align: center;">
                <h3 style="margin-bottom: 1rem;">Glassy Design</h3>
                <p style="color: var(--text-dim); font-size: 0.95rem;">A modern, premium interface that makes managing
                    your collection a visual delight.</p>
            </div>
            <div class="glass-card" style="text-align: center;">
                <h3 style="margin-bottom: 1rem;">Share Anywhere</h3>
                <p style="color: var(--text-dim); font-size: 0.95rem;">Generate unique public links to showcase your
                    collection to friends and family.</p>
            </div>
            <div class="glass-card" style="text-align: center;">
                <h3 style="margin-bottom: 1rem;">Smart Tracking</h3>
                <p style="color: var(--text-dim); font-size: 0.95rem;">Keep tabs on what you own, what you want, and
                    what you've finished across all media.</p>
            </div>
        </div>
    </div>

    <footer class="site-footer landing-footer">
        <p>&copy; <?php echo date('Y'); ?> MediaTracker. All rights reserved.</p>
    </footer>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>

</html>
