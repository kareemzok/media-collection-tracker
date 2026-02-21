# MediaTracker - Personal Media Collection Tracker

A premium, native PHP application with a glassy aesthetic to help you track your movies, music, and games.

## Features

- **User Authentication**: Secure login and registration.
- **Role Management**: Admin level for system overview and Normal user level for collections.
- **Media Management**: Add, edit, and delete media items with rich metadata.
- **Status Tracking**: Mark items as "Owned," "Wishlist," "Currently Using," or "Completed."
- **Advanced Search**: Filter and search your collection by title, creator, genre, or status.
- **Public Profiles**: Share your collection through unique public URLs.
- **Glassmorphism UI**: A stunning, modern design with blur effects and smooth transitions.
- **AI-Powered Suggestions (Mocked)**: Smart recommendations based on your collection trends.

## Setup Instructions

### 1. Database Configuration

1. Create a MySQL database named `media_tracker`.
2. Import the `schema.sql` file located in the root directory into your database.
3. Update `includes/db.php` with your database credentials (default: `root` with no password).

### 2. Running the App

1. Place the project folder in your web server's root (e.g., `htdocs` for XAMPP).
2. Start your Apache and MySQL servers.
3. Access the app via `http://localhost/personal-media-collection-tracker`.

### 3. Default Accounts (Seed Data)

- **Admin**: `admin` / `password`
- **User**: `kareem` / `password`

## Design Aesthetic

The application uses a **Glassmorphism** approach:

- Background: Radial dark gradients.
- Panels: Semi-transparent white with backdrop-blur.
- Typography: Outfit (Google Fonts).

## Creative Implementation

- **AI Recommendation Engine**: A mock logic in the dashboard that suggests media based on your most entered genres.
- **Collection Mix Visuals**: Visual progress bars in the admin panel to show the distribution of media types.
- **Profile Sharing Engine**: A dedicated `view-profile.php` that displays a user's collection publicly without exposing private data.
