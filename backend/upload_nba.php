<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../frontend/index.php?tab=nba&msg=Invalid request&type=error");
    exit;
}

$criteria = $_POST['criteria'];
$file = $_FILES['file'];

$uploadDir = __DIR__ . "/../uploads/nba/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$path = $uploadDir . time() . "_" . basename($file['name']);
move_uploaded_file($file['tmp_name'], $path);

header("Location: ../frontend/nba_page.php?criteria=" . urlencode($criteria) . "&msg=Uploaded Successfully&type=success");
exit;
