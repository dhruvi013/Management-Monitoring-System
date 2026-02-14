<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
$criteria = $input['criteria'] ?? null;

if (!$id || !$criteria) {
    echo json_encode(['success' => false, 'message' => 'Missing ID or criteria']);
    exit;
}

$table = '';
switch ($criteria) {
    case '3.1':
        $table = 'nba_criterion_31';
        break;
    case '3.2.1':
        $table = 'nba_criterion_321';
        break;
    case '3.2.2':
        $table = 'nba_criterion_322';
        break;
    case '3.3.1':
        $table = 'nba_criterion_331';
        break;
    case '3.3.2':
        $table = 'nba_criterion_332';
        break;
    case '4.1':
        $table = 'nba_enrollment_41';
        break;
    case '4.2.1':
        $table = 'nba_success_421';
        break;
    case '4.2.2':
        $table = 'nba_success_422';
        break;
    case '4.3':
        $table = 'nba_academic_43';
        break;
    case '4.4':
        $table = 'nba_placement_44';
        break;
    case '4.5.1':
        $table = 'nba_professional_451';
        break;
    case '4.5.2':
        $table = 'nba_publications_452';
        break;
    case '4.5.3':
        $table = 'nba_participation_453';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid criteria']);
        exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
    $result = $stmt->execute([':id' => $id]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
