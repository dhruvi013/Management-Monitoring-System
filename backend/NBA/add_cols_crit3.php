<?php
require_once '../db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $alterations = [
        'nba_criterion_31' => [
            "ADD COLUMN IF NOT EXISTS semester VARCHAR(20) AFTER academic_year",
            "ADD COLUMN IF NOT EXISTS subject_code VARCHAR(50) AFTER semester"
        ],
        'nba_criterion_321' => [
            "ADD COLUMN IF NOT EXISTS semester VARCHAR(20) AFTER academic_year"
        ],
        'nba_criterion_322' => [
            "ADD COLUMN IF NOT EXISTS semester VARCHAR(20) AFTER academic_year",
            "ADD COLUMN IF NOT EXISTS subject_code VARCHAR(50) AFTER semester"
        ],
        'nba_criterion_331' => [
            "ADD COLUMN IF NOT EXISTS semester VARCHAR(20) AFTER academic_year"
        ],
        'nba_criterion_332' => [
            "ADD COLUMN IF NOT EXISTS semester VARCHAR(20) AFTER academic_year"
        ]
    ];

    foreach ($alterations as $table => $sqls) {
        foreach ($sqls as $sql) {
            try {
                // Check if column exists first to avoid error in MySQL versions that don't support IF NOT EXISTS in ALTER
                // Simple parsing to get column name
                preg_match('/ADD COLUMN IF NOT EXISTS (\w+)/', $sql, $matches);
                if (empty($matches)) {
                     // Try standard syntax without IF NOT EXISTS if regex failed, or handle error
                     $col = explode(' ', $sql)[3]; // simplified assumption
                } else {
                    $col = $matches[1];
                }
                
                // SQLite/MySQL check (doing a try-catch is often easier for simple scripts)
                // For MySQL specifically:
                $check = $pdo->query("SHOW COLUMNS FROM $table LIKE '$col'");
                if ($check->rowCount() == 0) {
                     $finalSql = str_replace("IF NOT EXISTS ", "", $sql); // Remove IF NOT EXISTS for broad compatibility if we rely on check
                     $pdo->exec("ALTER TABLE $table $finalSql");
                     echo "Updated $table: Added $col\n";
                } else {
                     echo "Skipped $table: $col already exists\n";
                }
                
            } catch (PDOException $e) {
                echo "Error altering $table: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Database schema updated successfully.\n";

} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
