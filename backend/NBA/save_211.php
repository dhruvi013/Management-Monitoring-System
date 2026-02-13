<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $academic_year = $_POST['academic_year'];
    $description = $_POST['description'];
    
    // Optional: Calculate marks if applicable, but for text descriptions, marks might be manual or fixed.
    // For now, initializing with 0 or assuming manual entry later. 2.1.1 is 10 marks.
    $marks = 0; 

    if ($id) {
        $stmt = $pdo->prepare("UPDATE nba_criterion_211 SET academic_year=?, description=? WHERE id=?");
        $stmt->execute([$academic_year, $description, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO nba_criterion_211 (academic_year, description, marks) VALUES (?, ?, ?)");
        $stmt->execute([$academic_year, $description, $marks]);
    }

    header("Location: ../../frontend/nba_page.php?criteria=2.1.1&msg=Data Saved Successfully");
    exit();
}
?>
