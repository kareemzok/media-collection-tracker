<?php
require_once __DIR__ . '/partials/header.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . '/auth/login.php');
}

$id = $_GET['id'] ?? null;
$item = null;
$error = '';
$success = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM media_items WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $item = $stmt->fetch();
    if (!$item) {
        redirect(BASE_URL . '/media-list.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $creator = trim($_POST['creator']);
    $release_date = $_POST['release_date'] ?: null;
    $type = $_POST['type'];
    $genre = trim($_POST['genre']);
    $status = $_POST['status'];
    $rating = $_POST['rating'] ?: null;
    $notes = trim($_POST['notes']);

    if (empty($title)) {
        $error = "Title is required.";
    } else {
        if ($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE media_items SET title = ?, creator = ?, release_date = ?, type = ?, genre = ?, status = ?, rating = ?, notes = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$title, $creator, $release_date, $type, $genre, $status, $rating, $notes, $id, $_SESSION['user_id']])) {
                $success = "Item updated successfully!";
                // Refresh item data
                $stmt = $pdo->prepare("SELECT * FROM media_items WHERE id = ?");
                $stmt->execute([$id]);
                $item = $stmt->fetch();
            } else {
                $error = "Failed to update item.";
            }
        } else {
            // Create
            $stmt = $pdo->prepare("INSERT INTO media_items (user_id, title, creator, release_date, type, genre, status, rating, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $title, $creator, $release_date, $type, $genre, $status, $rating, $notes])) {
                $success = "Item added to collection!";
                header("Location: " . BASE_URL . "/media-list.php?success=1");
                exit();
            } else {
                $error = "Failed to add item.";
            }
        }
    }
}
?>

<div style="max-width: 600px; margin: 2rem auto;">
    <div style="margin-bottom: 2rem;">
        <a href="<?php echo BASE_URL; ?>/media-list.php"
            style="color: var(--text-dim); text-decoration: none; font-size: 0.9rem;">← Back to
            Collection</a>
        <h1 style="margin-top: 1rem;">
            <?php echo $id ? 'Edit Media' : 'Add to Collection'; ?>
        </h1>
    </div>

    <div class="glass-card">
        <?php if ($error): ?>
            <div
                style="background: rgba(239, 68, 68, 0.2); color: #f87171; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(239, 68, 68, 0.3);">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div
                style="background: rgba(34, 197, 94, 0.2); color: #4ade80; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(34, 197, 94, 0.3);">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" required
                        value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"
                        placeholder="e.g. Inception, Rumours, Elden Ring">
                </div>

                <div class="form-group">
                    <label>Creator / Studio</label>
                    <input type="text" name="creator" class="form-control"
                        value="<?php echo htmlspecialchars($item['creator'] ?? ''); ?>"
                        placeholder="e.g. Christopher Nolan">
                </div>

                <div class="form-group">
                    <label>Release Date</label>
                    <input type="date" name="release_date" class="form-control"
                        value="<?php echo $item['release_date'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Media Type</label>
                    <select name="type" class="form-control" style="appearance: none;">
                        <option value="movie" <?php echo ($item['type'] ?? '') === 'movie' ? 'selected' : ''; ?>>Movie
                        </option>
                        <option value="music" <?php echo ($item['type'] ?? '') === 'music' ? 'selected' : ''; ?>>Music
                        </option>
                        <option value="game" <?php echo ($item['type'] ?? '') === 'game' ? 'selected' : ''; ?>>Game
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" class="form-control"
                        value="<?php echo htmlspecialchars($item['genre'] ?? ''); ?>" placeholder="e.g. Sci-Fi, RPG">
                </div>

                <div class="form-group">
                    <label>Collection Status</label>
                    <select name="status" class="form-control" style="appearance: none;">
                        <option value="owned" <?php echo ($item['status'] ?? '') === 'owned' ? 'selected' : ''; ?>>Owned
                        </option>
                        <option value="wishlist" <?php echo ($item['status'] ?? '') === 'wishlist' ? 'selected' : ''; ?>>
                            Wishlist</option>
                        <option value="currently using" <?php echo ($item['status'] ?? '') === 'currently using' ? 'selected' : ''; ?>>Currently Using</option>
                        <option value="completed" <?php echo ($item['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Rating (1-5)</label>
                    <select name="rating" class="form-control" style="appearance: none;">
                        <option value="">No Rating</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($item['rating'] ?? '') == $i ? 'selected' : ''; ?>>
                                <?php echo str_repeat('★', $i); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Notes / Review</label>
                <textarea name="notes" class="form-control" rows="4"
                    placeholder="What do you think about this?"><?php echo htmlspecialchars($item['notes'] ?? ''); ?></textarea>
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <?php echo $id ? 'Save Changes' : 'Add Item'; ?>
                </button>
                <a href="<?php echo BASE_URL; ?>/media-list.php" class="btn btn-glass"
                    style="flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>