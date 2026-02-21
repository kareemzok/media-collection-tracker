<?php
require_once __DIR__ . '/partials/header.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . '/auth/login.php');
}

$userId = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$order_by = $_GET['order_by'] ?? 'title';

$allowed_orders = [
    'title' => 'title ASC',
    'date_desc' => 'release_date DESC',
    'date_asc' => 'release_date ASC',
    'rating' => 'rating DESC',
    'type' => 'type ASC'
];

$order_sql = $allowed_orders[$order_by] ?? 'title ASC';

$query = "SELECT * FROM media_items WHERE user_id = ?";
$params = [$userId];

if ($search) {
    $query .= " AND (title LIKE ? OR creator LIKE ? OR genre LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type) {
    $query .= " AND type = ?";
    $params[] = $type;
}

if ($status) {
    $query .= " AND status = ?";
    $params[] = $status;
}

$query .= " ORDER BY $order_sql";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>My Collection</h1>
    <a href="<?php echo BASE_URL; ?>/media-edit.php" class="btn btn-primary">+ Add New Item</a>
</div>

<!-- Filters -->
<div class="glass-card" style="padding: 1.5rem; margin-bottom: 2rem;">
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label
                style="font-size: 0.8rem; color: var(--text-dim); display: block; margin-bottom: 0.4rem;">Search</label>
            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>"
                placeholder="Title, creator, or genre...">
        </div>
        <div style="width: 150px;">
            <label
                style="font-size: 0.8rem; color: var(--text-dim); display: block; margin-bottom: 0.4rem;">Type</label>
            <select name="type" class="form-control" style="appearance: none;">
                <option value="">All Types</option>
                <option value="movie" <?php echo $type === 'movie' ? 'selected' : ''; ?>>Movie</option>
                <option value="music" <?php echo $type === 'music' ? 'selected' : ''; ?>>Music</option>
                <option value="game" <?php echo $type === 'game' ? 'selected' : ''; ?>>Game</option>
            </select>
        </div>
        <div style="width: 150px;">
            <label
                style="font-size: 0.8rem; color: var(--text-dim); display: block; margin-bottom: 0.4rem;">Status</label>
            <select name="status" class="form-control" style="appearance: none;">
                <option value="">All Status</option>
                <option value="owned" <?php echo $status === 'owned' ? 'selected' : ''; ?>>Owned</option>
                <option value="wishlist" <?php echo $status === 'wishlist' ? 'selected' : ''; ?>>Wishlist</option>
                <option value="currently using" <?php echo $status === 'currently using' ? 'selected' : ''; ?>>Using
                </option>
                <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
        <div style="width: 180px;">
            <label style="font-size: 0.8rem; color: var(--text-dim); display: block; margin-bottom: 0.4rem;">Order
                By</label>
            <select name="order_by" class="form-control" style="appearance: none;">
                <option value="title" <?php echo $order_by === 'title' ? 'selected' : ''; ?>>Title (A-Z)</option>
                <option value="date_desc" <?php echo $order_by === 'date_desc' ? 'selected' : ''; ?>>Newest First</option>
                <option value="date_asc" <?php echo $order_by === 'date_asc' ? 'selected' : ''; ?>>Oldest First</option>
                <option value="rating" <?php echo $order_by === 'rating' ? 'selected' : ''; ?>>Highest Rating</option>
                <option value="type" <?php echo $order_by === 'type' ? 'selected' : ''; ?>>Media Type</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="<?php echo BASE_URL; ?>/media-list.php" class="btn btn-glass">Clear</a>
    </form>
</div>

<div class="grid">
    <?php if (empty($items)): ?>
        <div class="glass-card" style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
            <p style="color: var(--text-dim);">No items found matching your criteria.</p>
        </div>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="glass-card media-card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <span class="badge badge-<?php echo str_replace(' ', '', $item['status']); ?>">
                        <?php echo ucfirst($item['status']); ?>
                    </span>
                    <span style="font-size: 0.75rem; color: var(--text-dim); font-weight: 600;">
                        <?php echo strtoupper($item['type']); ?>
                    </span>
                </div>

                <h3 style="margin-bottom: 0.25rem; font-size: 1.1rem; word-break: break-word;">
                    <?php echo htmlspecialchars($item['title']); ?>
                </h3>
                <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    <?php echo htmlspecialchars($item['creator']); ?>
                </p>

                <?php if ($item['genre']): ?>
                    <p
                        style="font-size: 0.8rem; background: rgba(255,255,255,0.05); display: inline-block; padding: 2px 8px; border-radius: 4px; margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($item['genre']); ?>
                    </p>
                <?php endif; ?>

                <?php if ($item['rating']): ?>
                    <div style="color: #fbbf24; margin-bottom: 1rem;">
                        <?php echo str_repeat('★', $item['rating']) . str_repeat('☆', 5 - $item['rating']); ?>
                    </div>
                <?php endif; ?>

                <div <div
                    style="margin-top: auto; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--glass-border); padding-top: 1rem;">
                    <a href="<?php echo BASE_URL; ?>/media-edit.php?id=<?php echo $item['id']; ?>" class="btn btn-glass"
                        style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">Edit</a>
                    <form method="POST" action="<?php echo BASE_URL; ?>/auth/delete-item.php"
                        onsubmit="return confirm('Are you sure?');" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit"
                            style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 0.85rem; font-weight: 600;">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>