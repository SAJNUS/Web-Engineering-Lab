<?php
// DB config
const DB_HOST = '127.0.0.1';
const DB_NAME = 'biodata_app2';
const DB_USER = 'root';
const DB_PASS = ''; // set your password if any

// PDO
function db(): PDO {
  static $pdo;
  if (!$pdo) {
    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}

// Session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Auth guard
function require_login() {
  if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
  }
}

// Helpers
function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// File upload helper
function save_upload($file, $userId): ?string {
  if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
  if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload error');

  // Validate size (<=2MB)
  if ($file['size'] > 2 * 1024 * 1024) throw new RuntimeException('Max 2MB allowed');

  // Validate mime
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
  if (!isset($allowed[$mime])) throw new RuntimeException('Only JPG/PNG/WebP');

  // Ensure folder
  $dir = __DIR__ . '/uploads/' . $userId;
  if (!is_dir($dir)) mkdir($dir, 0775, true);

  $basename = bin2hex(random_bytes(8)).'.'.$allowed[$mime];
  $path = $dir . '/' . $basename;
  if (!move_uploaded_file($file['tmp_name'], $path)) throw new RuntimeException('Move failed');

  // Return web path
  return 'uploads/'.$userId.'/'.$basename;
}
