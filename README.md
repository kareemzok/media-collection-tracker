# MediaTracker - Personal Media Collection Tracker

A premium, native PHP application with a glassy aesthetic to help you track your movies, music, and games.

## Features

- **User Authentication & Role Access**: Secure registration/login with password hashing, plus role-based admin access.
- **Profile & Public Sharing**: Editable public bio, public profile page, copyable share URL, and social sharing buttons (WhatsApp, Facebook, LinkedIn).
- **Media Collection CRUD**: Add, edit, delete, and list Movies, Music, and Games with metadata (creator, genre, release date, rating, notes).
- **Status Tracking**: Organize items by "Owned", "Wishlist", "Currently Using", or "Completed".
- **Filtering, Search & Sorting**: Filter by type/status, search by title/creator/genre, and sort by title/date/rating/type.
- **Personal Dashboard**: See collection stats, wishlist/completed counts, and recently added items.
- **AI Recommendations (OpenAI)**: Generate personalized recommendations with per-user daily limits and one-click add-to-wishlist.
- **Admin Dashboard**: View total users/items, recent users, and media type distribution.
- **Error Logging**: Centralized PHP error/exception/fatal logging to `storage/error.log`.
- **Internal Activity Log**: Records recommendation-based additions in the `activity_log` table.

## Setup Instructions (Local)

### 1. Database Configuration

1. Create a MySQL database named `media_tracker`.
2. Import the `schema.sql` file located in the root directory.
3. Run `php seed.php` to load default accounts and demo media data (optional, but recommended for local testing).
4. Rename `.env.example` to `.env` (or create a `.env` file) and update with your credentials:

   ```env
   DB_HOST=localhost
   DB_NAME=media_tracker
   DB_USER=root
   DB_PASS=your_password

   AI_ENABLED=true
   OPENAI_API_KEY=your_key_here
   ```

### 2. Running Locally

1. Place the project folder in your web server's root (e.g., `htdocs` for XAMPP).
2. Start Apache and MySQL.
3. Access via `http://localhost/your-project-folder`.

---

## Deployment to cPanel

### Method 1: Manual Upload (FTP/File Manager)

1. **Upload Files**: Compress your project files (excluding `.git` and local `.env`) and upload them to `public_html/` or a subdirectory via cPanel File Manager.
2. **Database Setup**:
   - Use **MySQL® Database Wizard** in cPanel to create a database and user.
   - Assign the user to the database with **All Privileges**.
   - Import `schema.sql` using **phpMyAdmin**.
   - Run `php seed.php` (or execute the script once from terminal) if you want demo accounts and sample media.
3. **Environment Configuration**:
   - Create a `.env` file in the remote directory.
   - Enter your cPanel database details and OpenAI API key.
4. **Base URL**: Ensure `includes/db.php` or your config correctly detects the subdirectory if not on the root domain.

### Method 2: Git Deployment (Recommended)

1. **Git Version Control**: In cPanel, go to **Git™ Version Control**.
2. **Clone Repository**: Provide your repository URL (e.g., from GitHub/GitLab).
3. **Deployment Script**: Create a `.cpanel.yml` file in your repository if you need to move files to `public_html` automatically:
   ```yaml
   ---
   deployment:
     tasks:
       - export DEPLOYPATH=/home/username/public_html/
       - /bin/cp -R * $DEPLOYPATH
   ```
4. **Setup Environment**: Manually create the `.env` file on the server as it should never be tracked in Git.

---

## Default Accounts (Seed Data)

- **Admin**: `admin` / `password`
- **User**: `kareem` / `password`

## Design Aesthetic

The application uses a **Glassmorphism** approach:

- Background: Radial dark gradients.
- Panels: Semi-transparent white with backdrop-blur.
- Typography: Outfit (Google Fonts).
