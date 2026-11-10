<?php
// Shared database connection for all six practical laboratories.
$host = 'localhost';
$dbname = 'php_practical_labs';
$username = 'root';
$password = ''; // Change this if your MySQL account has a password.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Check config.php and import database.sql. Details: ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function post_value($key) {
    return trim($_POST[$key] ?? '');
}
function valid_length($value, $min, $max) {
    $length = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    return $length >= $min && $length <= $max;
}
function upload_image($field, $folder, $maxBytes = 2097152) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image could not be uploaded.');
    }
    if ($_FILES[$field]['size'] > $maxBytes) {
        throw new RuntimeException('The image is larger than the allowed size.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES[$field]['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($allowed[$mime]) || getimagesize($_FILES[$field]['tmp_name']) === false) {
        throw new RuntimeException('Only valid JPEG and PNG images are allowed.');
    }
    $directory = __DIR__ . '/' . trim($folder, '/');
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('Upload folder could not be created.');
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $directory . '/' . $filename)) {
        throw new RuntimeException('Could not save the uploaded image.');
    }
    return trim($folder, '/') . '/' . $filename;
}
