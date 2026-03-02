<?php
require_once __DIR__ . '/logger.php';

// Load environment variables from .env file
$env_path = __DIR__ . '/../.env';
if (file_exists($env_path)) {
    $env = parse_ini_file($env_path);
    foreach ($env as $key => $value) {
        if (!defined($key)) {
            define($key, $value);
        }
    }
}

// Database configuration with defaults from environment or fallback
if (!defined('DB_HOST'))
    define('DB_HOST', 'localhost');
if (!defined('DB_NAME'))
    define('DB_NAME', '');
if (!defined('DB_USER'))
    define('DB_USER', '');
if (!defined('DB_PASS'))
    define('DB_PASS', '');

// AI Configuration
if (!defined('OPENAI_API_KEY'))
    define('OPENAI_API_KEY', '');
if (!defined('OPENAI_MODEL'))
    define('OPENAI_MODEL', '');
if (!defined('OPENAI_TEMPERATURE'))
    define('OPENAI_TEMPERATURE', 0.7);
if (!defined('AI_ENABLED'))
    define('AI_ENABLED', 'true');
if (!defined('AI_DAILY_LIMIT'))
    define('AI_DAILY_LIMIT', 3);

// Global AI Toggle logic
$val = strtolower(AI_ENABLED);
define('AI_ENABLED_BOOL', !($val === 'false' || $val === '0' || $val === 'off' || $val === ''));

// Path handling: Detect the project root relative to the web root
$project_root = str_replace('\\', '/', dirname(__DIR__));
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');

// Calculate the relative path from the document root
$base_path = str_ireplace($doc_root, '', $project_root);
$base_path = '/' . trim($base_path, '/');
if ($base_path === '/')
    $base_path = '';

if (!defined('BASE_URL'))
    define('BASE_URL', $base_path);

try {
    // Only attempt connection if DB_NAME is provided
    if (empty(DB_NAME)) {
        throw new Exception("Database name not configured in .env");
    }
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // If we're in a browser, we might want to show a nicer error, but for now:
    if (php_sapi_name() !== 'cli' && !headers_sent()) {
        // Optional: redirect to a setup page or show a clean error
    }
    // die("Connection failed: " . $e->getMessage()); 
    // Commented out die to allow the app to potentially run without DB if needed, 
    // but most pages will fail later anyway. Keeping it for now as it was there.
    die("Configuration Error: " . $e->getMessage());
}
