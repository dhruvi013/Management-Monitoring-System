<?php
// backend/upload_nba.php
require_once __DIR__ . '/db.php';

function redirect_back($criteria, $sub = null, $msg = '', $type = 'success') {
    $url = '../frontend/nba_page.php?';
    if ($criteria) $url .= 'criteria=' . urlencode($criteria);
    if ($sub) $url .= '&subcriteria=' . urlencode($sub);
    $url .= '&msg=' . urlencode($msg) . '&type=' . $type;
    header("Location: $url");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../frontend/index.php?tab=nba&msg=Invalid+request&type=error");
    exit;
}

$criteria = $_POST['criteria'] ?? null;
$sub = $_POST['subcriteria'] ?? null;

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    redirect_back($criteria, $sub, 'File upload failed', 'error');
}

// Basic validation: limit size (example 10MB)
$maxBytes = 10 * 1024 * 1024;
if ($_FILES['file']['size'] > $maxBytes) {
    redirect_back($criteria, $sub, 'File too large (max 10MB)', 'error');
}

// Create upload folder
$uploadDir = __DIR__ . '/../uploads/nba/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Sanitize filename
$originalName = basename($_FILES['file']['name']);
$ext = pathinfo($originalName, PATHINFO_EXTENSION);
$storedName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$destination = $uploadDir . $storedName;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
    redirect_back($criteria, $sub, 'Failed to move uploaded file', 'error');
}

// Save record in DB
$stmt = $pdo->prepare("INSERT INTO nba_files (criteria, subcriteria, original_name, file_path) VALUES (?, ?, ?, ?)");
$stmt->execute([$criteria, $sub, $originalName, 'uploads/nba/' . $storedName]);

redirect_back($criteria, $sub, 'File uploaded successfully', 'success');
