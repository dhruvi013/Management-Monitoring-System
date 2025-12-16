<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$criteria = $_GET['criteria'] ?? '';

$response = [];

try {
    if ($criteria === '4.1') {
        $stmt = $pdo->query("SELECT * FROM nba_enrollment_41 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    } 
    elseif ($criteria === '4.2.1') {
        $stmt = $pdo->query("SELECT * FROM nba_success_421 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '4.2.2') {
        $stmt = $pdo->query("SELECT * FROM nba_success_422 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '4.3') {
        $stmt = $pdo->query("SELECT * FROM nba_academic_43 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '4.4') {
        $stmt = $pdo->query("SELECT * FROM nba_placement_44 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '4.5.1') {
        $stmt = $pdo->query("SELECT * FROM nba_professional_451 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '4.5.2') {
        $stmt = $pdo->query("SELECT * FROM nba_publications_452 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '4.5.3') {
        $stmt = $pdo->query("SELECT * FROM nba_participation_453 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '5.1') {
        $stmt = $pdo->query("SELECT * FROM nba_criterion_51 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    elseif ($criteria === '5.2') {
        $stmt = $pdo->query("SELECT * FROM nba_criterion_52 ORDER BY academic_year DESC");
        $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    else {
        $response = ['success' => false, 'message' => 'Invalid criteria'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
