<?php
require_once __DIR__ . '/partials/header.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . '/auth/login.php');
}

$userId = $_SESSION['user_id'];
$success = '';

// Handle Bio update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $bio = trim($_POST['bio']);
    $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->execute([$bio, $userId]);
    $success = "Profile updated!";
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Your Profile</h1>
        <a href="<?php echo BASE_URL; ?>/view-profile.php?user=<?php echo $user['username']; ?>" class="btn btn-primary"
            target="_blank">View
            Public Profile</a>
    </div>

    <?php if ($success): ?>
        <div
            style="background: rgba(34, 197, 94, 0.2); color: #4ade80; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(34, 197, 94, 0.3);">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="grid" style="grid-template-columns: 1fr 2fr; gap: 2rem;">
        <div class="glass-card" style="text-align: center;">
            <div
                style="width: 100px; height: 100px; background: var(--primary); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700;">
                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
            </div>
            <h3>
                <?php echo htmlspecialchars($user['username']); ?>
            </h3>
            <p style="color: var(--text-dim); font-size: 0.9rem;">
                <?php echo htmlspecialchars($user['email']); ?>
            </p>
            <p style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-dim);">Joined
                <?php echo date('M Y', strtotime($user['created_at'])); ?>
            </p>
        </div>

        <div class="glass-card">
            <form method="POST">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label>Public Bio / About Me</label>
                    <textarea name="bio" class="form-control" rows="6"
                        placeholder="Tell the world about your collection..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Profile</button>
            </form>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <h4 style="margin-bottom: 1rem;">Share Your Collection</h4>
                <?php $shareUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . BASE_URL . "/view-profile.php?user=" . $user['username']; ?>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <input type="text" id="shareUrl" class="form-control" readonly value="<?php echo $shareUrl; ?>">
                    <button onclick="copyShareUrl()" class="btn btn-glass"
                        style="white-space: nowrap; padding: 0.75rem 1rem;">Copy</button>
                </div>
                <div id="copyNotice" class="copy-success">✓ Link copied to clipboard!</div>

                <div class="share-btn-group">
                    <a href="https://wa.me/?text=Check%20out%20my%20media%20collection%20vault%20on%20MediaTracker!%20<?php echo urlencode($shareUrl); ?>"
                        target="_blank" class="btn btn-whatsapp"
                        style="flex: 1; text-align: center; font-size: 0.85rem;">
                        WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($shareUrl); ?>"
                        target="_blank" class="btn btn-facebook"
                        style="flex: 1; text-align: center; font-size: 0.85rem;">
                        Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($shareUrl); ?>"
                        target="_blank" class="btn btn-linkedin"
                        style="flex: 1; text-align: center; font-size: 0.85rem;">
                        LinkedIn
                    </a>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-dim); margin-top: 1rem;">Sharing your collection allows
                    others to see your "Owned" and "Completed" items.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>