<?php
require_once __DIR__ . '/includes/db.php';

$username = $_GET['user'] ?? null;
$error = '';
$user = null;
$items = [];

if ($username) {
    $stmt = $pdo->prepare("SELECT id, username, bio, created_at FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("SELECT * FROM media_items WHERE user_id = ? AND status != 'wishlist' ORDER BY created_at DESC");
        $stmt->execute([$user['id']]);
        $items = $stmt->fetchAll();
    } else {
        $error = "User not found.";
    }
} else {
    $error = "No user specified.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $user ? htmlspecialchars($user['username']) . "'s Collection" : "Collection Not Found"; ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>

<body style="padding-top: 2rem;">
    <div class="container">
        <?php if ($error): ?>
            <div class="glass-card" style="text-align: center; padding: 4rem;">
                <h1>Oops!</h1>
                <p style="color: var(--text-dim); margin-top: 1rem;">
                    <?php echo $error; ?>
                </p>
                <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary" style="margin-top: 2rem;">Back to Home</a>
            </div>
        <?php else: ?>
            <div class="glass-card" style="margin-bottom: 3rem; text-align: center;">
                <h1 style="font-size: 2.5rem;">
                    <?php echo htmlspecialchars($user['username']); ?>
                </h1>
                <p style="color: var(--text-dim); margin-top: 0.5rem;">Member since
                    <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </p>
                <?php if ($user['bio']): ?>
                    <p style="margin-top: 1.5rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                        <?php echo nl2br(htmlspecialchars($user['bio'])); ?>
                    </p>
                <?php endif; ?>
            </div>

            <h2 style="margin-bottom: 2rem;">Current Collection</h2>
            <div class="grid">
                <?php if (empty($items)): ?>
                    <p style="color: var(--text-dim); grid-column: 1 / -1; text-align: center;">This collection is currently
                        private or empty.</p>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <div class="glass-card" style="padding: 1.5rem; position: relative;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <span
                                    style="font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: uppercase;">
                                    <?php echo htmlspecialchars($item['type']); ?>
                                </span>
                                <?php if ($item['rating']): ?>
                                    <span
                                        style="color: #fbbf24; font-size: 0.8rem;"><?php echo str_repeat('★', $item['rating']); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 1rem;">
                                <?php echo htmlspecialchars($item['creator']); ?>
                            </p>

                            <a href="https://wa.me/?text=Check%20out%20this%20<?php echo $item['type']; ?>%20in%20my%20collection:%20<?php echo urlencode($item['title']); ?>"
                                target="_blank" class="btn btn-glass"
                                style="font-size: 0.7rem; padding: 0.3rem 0.6rem; margin-top: 1rem;">Share Media</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 4rem;">
                <p style="color: var(--text-dim); font-size: 0.85rem;">Want to create your own tracker?</p>
                <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-glass" style="margin-top: 1rem;">Join
                    MediaTracker Today</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>