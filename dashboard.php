<?php
require_once __DIR__ . '/partials/header.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . '/auth/login.php');
}

$userId = $_SESSION['user_id'];

// Get some stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM media_items WHERE user_id = ?");
$stmt->execute([$userId]);
$totalItems = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM media_items WHERE user_id = ? AND status = 'wishlist'");
$stmt->execute([$userId]);
$wishlistCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM media_items WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$userId]);
$completedCount = $stmt->fetchColumn();

// Get recent items
$stmt = $pdo->prepare("SELECT * FROM media_items WHERE user_id = ? ORDER BY created_at DESC LIMIT 4");
$stmt->execute([$userId]);
$recentItems = $stmt->fetchAll();

// Get AI Usage stats
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT usage_count FROM ai_usage WHERE user_id = ? AND usage_date = ?");
$stmt->execute([$userId, $today]);
$aiUsage = $stmt->fetchColumn() ?: 0;
$aiLimit = defined('AI_DAILY_LIMIT') ? (int) AI_DAILY_LIMIT : 3;
$aiRemaining = max(0, $aiLimit - $aiUsage);
?>

<div style="margin-bottom: 3rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Hello,
        <?php echo htmlspecialchars($_SESSION['username']); ?>!
    </h1>
    <p style="color: var(--text-dim);">Welcome to your media collection dashboard.</p>
</div>

<div class="grid" style="margin-bottom: 4rem;">
    <div class="glass-card" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-dim); font-size: 0.9rem; text-transform: uppercase;">Total Items</h3>
        <p style="font-size: 2.5rem; font-weight: 700; margin: 0.5rem 0;">
            <?php echo $totalItems; ?>
        </p>
    </div>
    <div class="glass-card" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-dim); font-size: 0.9rem; text-transform: uppercase;">Wishlist</h3>
        <p style="font-size: 2.5rem; font-weight: 700; margin: 0.5rem 0; color: #facc15;">
            <?php echo $wishlistCount; ?>
        </p>
    </div>
    <div class="glass-card" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-dim); font-size: 0.9rem; text-transform: uppercase;">Completed</h3>
        <p style="font-size: 2.5rem; font-weight: 700; margin: 0.5rem 0; color: #a855f7;">
            <?php echo $completedCount; ?>
        </p>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Recently Added</h2>
    <a href="<?php echo BASE_URL; ?>/media-list.php"
        style="color: var(--primary); text-decoration: none; font-weight: 600;">View All</a>
</div>

<div class="grid">
    <?php if (empty($recentItems)): ?>
        <div class="glass-card" style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
            <p style="color: var(--text-dim); margin-bottom: 1.5rem;">Your collection is empty.</p>
            <a href="<?php echo BASE_URL; ?>/media-edit.php" class="btn btn-primary">Add Your First Media</a>
        </div>
    <?php else: ?>
        <?php foreach ($recentItems as $item): ?>
            <div class="glass-card media-card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <span class="badge badge-<?php echo str_replace(' ', '', $item['status']); ?>">
                        <?php echo ucfirst($item['status']); ?>
                    </span>
                    <span style="font-size: 0.75rem; color: var(--text-dim);">
                        <?php echo strtoupper($item['type']); ?>
                    </span>
                </div>
                <h3 style="margin-bottom: 0.25rem; font-size: 1.1rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($item['creator']); ?></p>
                <div style="margin-top: auto;">
                    <a href="<?php echo BASE_URL; ?>/media-edit.php?id=<?php echo $item['id']; ?>" class="btn btn-glass"
                        style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">Edit Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="glass-card" style="margin-top: 4rem; padding: 2rem;">
<<<<<<< HEAD
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
        <div>
            <h3 style="margin-bottom: 0.5rem; <?php echo !AI_ENABLED_BOOL ? 'color: var(--text-dim);' : ''; ?>">Smart
                Recommendations</h3>
            <p style="color: var(--text-dim); font-size: 0.95rem;">
                <?php if (AI_ENABLED_BOOL): ?>
                    Get personalized suggestions based on your collection.
                <?php else: ?>
                    AI recommendations are currently disabled.
                <?php endif; ?>
            </p>
        </div>
        <button id="getAiRecommendations"
            class="btn <?php echo (AI_ENABLED_BOOL && $aiRemaining > 0) ? 'btn-primary' : 'btn-glass'; ?>"
            style="display: flex; align-items: center;" <?php echo (!AI_ENABLED_BOOL || $aiRemaining <= 0) ? 'disabled' : ''; ?>>
            <span id="btnText">
                <?php
                if (!AI_ENABLED_BOOL) {
                    echo 'AI Recommendations Disabled';
                } elseif ($aiRemaining <= 0) {
                    echo 'Daily Limit Reached';
                } else {
                    echo 'Get AI Recommendations';
                }
                ?>
            </span>
        </button>
    </div>

    <?php if (AI_ENABLED_BOOL): ?>
        <div style="margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--text-dim);">
            <span id="aiUsageInfo">
                Remaining today: <strong id="aiRemainingCount"><?php echo $aiRemaining; ?></strong> /
                <?php echo $aiLimit; ?>
            </span>
        </div>
    <?php endif; ?>

    <div id="aiRecommendationResults" class="recommendation-container" style="display: none;">
        <!-- Recommendations will be injected here -->
    </div>

    <p id="aiStatusMessage" style="color: var(--primary); font-size: 0.8rem; margin-top: 1rem; display: none;"></p>
=======
    <h3>Quick AI Suggestion (Draft)</h3>
    <p style="color: var(--text-dim); font-size: 0.95rem; margin-top: 0.5rem;">
        Based on your preference for <strong>Sci-Fi movies</strong>, you might enjoy <strong>"Blade Runner
            2049"</strong>.
        <span style="color: var(--primary); font-size: 0.8rem; display: block; margin-top: 0.5rem;">💡 This is a
            placeholder for the Smart Recommendation engine.</span>
    </p>
>>>>>>> parent of bf47a19 (Implement env file for variable)
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>