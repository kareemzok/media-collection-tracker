<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    fwrite(STDERR, "Database connection is not available.\n");
    exit(1);
}

$defaultPassword = 'password';
$passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);

$users = [
    [
        'username' => 'admin',
        'email' => 'admin@example.com',
        'role' => 'admin',
        'bio' => 'System Administrator',
    ],
    [
        'username' => 'kareem',
        'email' => 'kareem@example.com',
        'role' => 'user',
        'bio' => 'Media enthusiast and collector.',
    ],
];

$mediaSeed = [
    [
        'username' => 'kareem',
        'title' => 'Inception',
        'creator' => 'Christopher Nolan',
        'release_date' => '2010-07-16',
        'type' => 'movie',
        'genre' => 'Sci-Fi',
        'status' => 'completed',
        'rating' => 5,
        'notes' => 'Masterpiece of modern cinema.',
    ],
    [
        'username' => 'kareem',
        'title' => 'The Legend of Zelda: Breath of the Wild',
        'creator' => 'Nintendo',
        'release_date' => '2017-03-03',
        'type' => 'game',
        'genre' => 'Action-Adventure',
        'status' => 'currently using',
        'rating' => 5,
        'notes' => 'Best open world game ever.',
    ],
    [
        'username' => 'kareem',
        'title' => 'Rumours',
        'creator' => 'Fleetwood Mac',
        'release_date' => '1977-02-04',
        'type' => 'music',
        'genre' => 'Rock',
        'status' => 'owned',
        'rating' => 5,
        'notes' => 'The ultimate breakup album.',
    ],
    [
        'username' => 'kareem',
        'title' => 'Interstellar',
        'creator' => 'Christopher Nolan',
        'release_date' => '2014-11-07',
        'type' => 'movie',
        'genre' => 'Sci-Fi',
        'status' => 'wishlist',
        'rating' => null,
        'notes' => 'Need to buy the 4K version.',
    ],
];

$userIds = [];
$createdUsers = 0;
$updatedUsers = 0;
$createdMedia = 0;
$updatedMedia = 0;

try {
    $pdo->beginTransaction();

    $selectUserStmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $insertUserStmt = $pdo->prepare(
        'INSERT INTO users (username, email, password, role, bio) VALUES (?, ?, ?, ?, ?)'
    );
    $updateUserStmt = $pdo->prepare(
        'UPDATE users SET email = ?, password = ?, role = ?, bio = ? WHERE id = ?'
    );

    foreach ($users as $user) {
        $selectUserStmt->execute([$user['username']]);
        $existing = $selectUserStmt->fetch();

        if ($existing) {
            $updateUserStmt->execute([
                $user['email'],
                $passwordHash,
                $user['role'],
                $user['bio'],
                $existing['id'],
            ]);
            $userIds[$user['username']] = (int) $existing['id'];
            $updatedUsers++;
            continue;
        }

        $insertUserStmt->execute([
            $user['username'],
            $user['email'],
            $passwordHash,
            $user['role'],
            $user['bio'],
        ]);
        $userIds[$user['username']] = (int) $pdo->lastInsertId();
        $createdUsers++;
    }

    $selectMediaStmt = $pdo->prepare(
        'SELECT id FROM media_items WHERE user_id = ? AND title = ? AND type = ? LIMIT 1'
    );
    $insertMediaStmt = $pdo->prepare(
        'INSERT INTO media_items (user_id, title, creator, release_date, type, genre, status, rating, notes)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $updateMediaStmt = $pdo->prepare(
        'UPDATE media_items
         SET creator = ?, release_date = ?, genre = ?, status = ?, rating = ?, notes = ?
         WHERE id = ?'
    );

    foreach ($mediaSeed as $item) {
        if (!isset($userIds[$item['username']])) {
            continue;
        }

        $userId = $userIds[$item['username']];
        $selectMediaStmt->execute([$userId, $item['title'], $item['type']]);
        $existing = $selectMediaStmt->fetch();

        if ($existing) {
            $updateMediaStmt->execute([
                $item['creator'],
                $item['release_date'],
                $item['genre'],
                $item['status'],
                $item['rating'],
                $item['notes'],
                $existing['id'],
            ]);
            $updatedMedia++;
            continue;
        }

        $insertMediaStmt->execute([
            $userId,
            $item['title'],
            $item['creator'],
            $item['release_date'],
            $item['type'],
            $item['genre'],
            $item['status'],
            $item['rating'],
            $item['notes'],
        ]);
        $createdMedia++;
    }

    $pdo->commit();

    echo "Seed complete.\n";
    echo "Users created: {$createdUsers}, updated: {$updatedUsers}\n";
    echo "Media created: {$createdMedia}, updated: {$updatedMedia}\n";
    echo "Default login: admin / {$defaultPassword}, kareem / {$defaultPassword}\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Seed failed: " . $e->getMessage() . "\n");
    exit(1);
}
