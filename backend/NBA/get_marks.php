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
    elseif ($criteria === '4.5.1') {
        $stmt = $pdo->query("SELECT * FROM nba_professional_451 ORDER BY created_at DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            $response = [
                'success' => true,
                'total_marks' => $data['total_marks'],
                'marks_a' => $data['marks_a'],
                'marks_b' => $data['marks_b'],
                'no_of_chapters' => $data['no_of_chapters'],
                'international_events' => $data['international_events'],
                'national_events' => $data['national_events'],
                'state_events' => $data['state_events'],
                'dept_events' => $data['dept_events'],
                'academic_year' => $data['academic_year']
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    }
    elseif ($criteria === '4.5.2') {
        $stmt = $pdo->query("SELECT * FROM nba_publications_452 ORDER BY created_at DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            $response = [
                'success' => true,
                'marks' => $data['marks'],
                'magazine' => $data['magazine'],
                'target_freq1' => $data['target_freq1'],
                'newsletter' => $data['newsletter'],
                'target_freq2' => $data['target_freq2'],
                'academic_year' => $data['academic_year']
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    }
    elseif ($criteria === '4.5.3') {
        $stmt = $pdo->query("SELECT * FROM nba_participation_453 ORDER BY created_at DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            // Get last 4 years for history
            $stmt = $pdo->query("SELECT academic_year, total_participation, within_state_percentage, outside_state_percentage, awards FROM nba_participation_453 ORDER BY created_at DESC LIMIT 4");
            $history = $stmt->fetchAll();
            
            $response = [
                'success' => true,
                'marks' => $data['marks'],
                'total_participation' => $data['total_participation'],
                'participation_within_state' => $data['participation_within_state'],
                'participation_outside_state' => $data['participation_outside_state'],
                'awards' => $data['awards'],
                'within_state_percentage' => $data['within_state_percentage'],
                'outside_state_percentage' => $data['outside_state_percentage'],
                'academic_year' => $data['academic_year'],
                'history' => $history
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    }
    elseif ($criteria === '5.1') {
        // Get latest 5.1 data
        $stmt = $pdo->query("SELECT * FROM nba_criterion_51 ORDER BY academic_year DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            // Get last 3 years for history
            $stmt = $pdo->query("SELECT academic_year, num_students, num_faculty, sfr FROM nba_criterion_51 ORDER BY academic_year DESC LIMIT 3");
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate Average SFR
            $avg_sfr = 0;
            $sfrs = array_column($history, 'sfr');
            if (count($sfrs) > 0) {
                 $avg_sfr = array_sum($sfrs) / count($sfrs);
            }
            
            $response = [
                'success' => true,
                'sfr' => $data['sfr'],
                'avg_sfr' => $avg_sfr,
                'num_students' => $data['num_students'],
                'num_faculty' => $data['num_faculty'],
                'academic_year' => $data['academic_year'],
                'history' => $history
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }
    }
    elseif ($criteria === '5.2') {
        $stmt = $pdo->query("SELECT * FROM nba_criterion_52 ORDER BY academic_year DESC LIMIT 1");
        $data = $stmt->fetch();
        
        if ($data) {
            // Get last 3 years for display history ONLY
            $stmt = $pdo->query("SELECT * FROM nba_criterion_52 ORDER BY academic_year DESC LIMIT 3");
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Use STORED calculations from the latest record
            // This ensures consistency with what was saved
            
            $response = [
                'success' => true,
                'cadre_marks' => $data['marks'],
                'avg_rf1' => $data['avg_rf1'], 'avg_af1' => $data['avg_af1'], 'r1' => $data['ratio1'],
                'avg_rf2' => $data['avg_rf2'], 'avg_af2' => $data['avg_af2'], 'r2' => $data['ratio2'],
                'avg_rf3' => $data['avg_rf3'], 'avg_af3' => $data['avg_af3'], 'r3' => $data['ratio3'],
                'history' => $history,
                'academic_year' => $data['academic_year']
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
