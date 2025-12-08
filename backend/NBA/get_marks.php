<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$criteria = $_GET['criteria'] ?? '';

$response = [];

try {
    if ($criteria === '4.1') {
        // Get latest 4.1 data
        $stmt = $pdo->query("SELECT * FROM nba_enrollment_41 ORDER BY created_at DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            // Get last 3 years for display
            $stmt = $pdo->query("SELECT academic_year, enrollment_ratio FROM nba_enrollment_41 ORDER BY created_at DESC LIMIT 3");
            $history = $stmt->fetchAll();
            
            $response = [
                'success' => true,
                'marks' => $data['marks'],
                'enrollment_ratio' => $data['enrollment_ratio'],
                'academic_year' => $data['academic_year'],
                'history' => $history
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    } 
    elseif ($criteria === '4.2') {
        // Get latest 4.2.1 data
        $stmt = $pdo->query("SELECT * FROM nba_success_421 ORDER BY created_at DESC LIMIT 1");
        $data_421 = $stmt->fetch();
        
        // Get latest 4.2.2 data
        $stmt = $pdo->query("SELECT * FROM nba_success_422 ORDER BY created_at DESC LIMIT 1");
        $data_422 = $stmt->fetch();
        
        if ($data_421 && $data_422) {
            $total_marks = $data_421['marks'] + $data_422['marks'];
            
            $response = [
                'success' => true,
                'marks_421' => $data_421['marks'],
                'marks_422' => $data_422['marks'],
                'total_marks' => $total_marks,
                'success_index_421' => $data_421['success_index'],
                'success_index_422' => $data_422['success_index'],
                'academic_year' => $data_421['academic_year']
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    }
    elseif ($criteria === '4.3') {
        // Get latest 4.3 data
        $stmt = $pdo->query("SELECT * FROM nba_academic_43 ORDER BY created_at DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            $response = [
                'success' => true,
                'marks' => $data['marks'],
                'api' => $data['api'],
                'mean_cgpa' => $data['total_mean_cgpa'],
                'success_rate' => $data['success_rate'],
                'academic_year' => $data['academic_year']
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    }
    elseif ($criteria === '4.4') {
        // Get latest 4.4 data
        $stmt = $pdo->query("SELECT * FROM nba_placement_44 ORDER BY created_at DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            // Get last 3 years for history
            $stmt = $pdo->query("SELECT academic_year, assessment_index, placed, higher_studies, entrepreneur, final_year_total FROM nba_placement_44 ORDER BY created_at DESC LIMIT 3");
            $history = $stmt->fetchAll();
            
            $response = [
                'success' => true,
                'marks' => $data['marks'],
                'assessment_index' => $data['assessment_index'],
                'placed' => $data['placed'],
                'higher_studies' => $data['higher_studies'],
                'entrepreneur' => $data['entrepreneur'],
                'final_year_total' => $data['final_year_total'],
                'academic_year' => $data['academic_year'],
                'history' => $history
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    }
    else {
        $response = ['success' => false, 'message' => 'Invalid criteria'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
