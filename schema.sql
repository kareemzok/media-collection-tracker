-- Database schema for Personal Media Collection Tracker

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    bio TEXT,
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS media_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    creator VARCHAR(255),
    release_date DATE,
    type ENUM('movie', 'music', 'game') NOT NULL,
    genre VARCHAR(100),
    status ENUM('owned', 'wishlist', 'currently using', 'completed') DEFAULT 'owned',
    rating INT CHECK (rating >= 1 AND rating <= 5),
    notes TEXT,
    cover_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    target_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ai_usage (
    user_id INT NOT NULL,
    usage_date DATE NOT NULL,
    usage_count INT DEFAULT 0,
    PRIMARY KEY (user_id, usage_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Note: Password for both is 'password'
INSERT INTO users (username, email, password, role, bio) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administrator'),
('kareem', 'kareem@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Media enthusiast and collector.');

-- Note: Password for both is 'password'

INSERT INTO media_items (user_id, title, creator, release_date, type, genre, status, rating, notes) VALUES 
(2, 'Inception', 'Christopher Nolan', '2010-07-16', 'movie', 'Sci-Fi', 'completed', 5, 'Masterpiece of modern cinema.'),
(2, 'The Legend of Zelda: Breath of the Wild', 'Nintendo', '2017-03-03', 'game', 'Action-Adventure', 'currently using', 5, 'Best open world game ever.'),
(2, 'Rumours', 'Fleetwood Mac', '1977-02-04', 'music', 'Rock', 'owned', 5, 'The ultimate breakup album.'),
(2, 'Interstellar', 'Christopher Nolan', '2014-11-07', 'movie', 'Sci-Fi', 'wishlist', NULL, 'Need to buy the 4K version.');


CREATE TABLE IF NOT EXISTS ai_usage (
    user_id INT NOT NULL,
    usage_date DATE NOT NULL,
    usage_count INT DEFAULT 0,
    PRIMARY KEY (user_id, usage_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
