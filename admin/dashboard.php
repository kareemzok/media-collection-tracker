<?php
require_once __DIR__ . '/../partials/header.php';

if (!isAdmin()) {
    redirect(BASE_URL . '/dashboard.php');
}

// Admin Stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalMedia = $pdo->query("SELECT COUNT(*) FROM media_items")->fetchColumn();
$recentUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Most popular media types
$stmt = $pdo->query("SELECT type, COUNT(*) as count FROM media_items GROUP BY type");
$typeStats = $stmt->fetchAll();
?>

<div style="margin-bottom: 3rem;">
    <h1>Admin Panel</h1>
    <p style="color: var(--text-dim);">System-wide overview and user management.</p>
</div>

<div class="grid" style="margin-bottom: 4rem;">
    <div class="glass-card" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-dim); font-size: 0.9rem; text-transform: uppercase;">Total Users</h3>
        <p style="font-size: 2.5rem; font-weight: 700; margin: 0.5rem 0;">
            <?php echo $totalUsers; ?>
        </p>
    </div>
    <div class="glass-card" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-dim); font-size: 0.9rem; text-transform: uppercase;">Total Items</h3>
        <p style="font-size: 2.5rem; font-weight: 700; margin: 0.5rem 0; color: var(--primary);">
            <?php echo $totalMedia; ?>
        </p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div class="glass-card">
        <h2 style="margin-bottom: 1.5rem;">Recent Users</h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr
                        style="border-bottom: 1px solid var(--glass-border); color: var(--text-dim); font-size: 0.85rem;">
                        <th style="padding: 1rem 0;">Username</th>
                        <th style="padding: 1rem 0;">Email</th>
                        <th style="padding: 1rem 0;">Joined</th>
                        <th style="padding: 1rem 0;">Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $user): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem;">
                            <td style="padding: 1rem 0; font-weight: 600;">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </td>
                            <td style="padding: 1rem 0; color: var(--text-dim);">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td style="padding: 1rem 0; color: var(--text-dim);">
                                <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td style="padding: 1rem 0;">
                                <span
                                    style="font-size: 0.75rem; background: <?php echo $user['role'] === 'admin' ? 'rgba(251, 191, 36, 0.2)' : 'rgba(255,255,255,0.1)'; ?>; color: <?php echo $user['role'] === 'admin' ? '#fbbf24' : 'var(--text-dim)'; ?>; padding: 2px 8px; border-radius: 4px;">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-card">
        <h2 style="margin-bottom: 1.5rem;">Collection Mix</h2>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($typeStats as $stat): ?>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 0.4rem;">
                        <span>
                            <?php echo ucfirst($stat['type']); ?>
                        </span>
                        <span style="color: var(--text-dim);">
                            <?php echo $stat['count']; ?> items
                        </span>
                    </div>
                    <div style="height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden;">
                        <div
                            style="width: <?php echo ($stat['count'] / max(1, $totalMedia)) * 100; ?>%; height: 100%; background: var(--primary);">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>