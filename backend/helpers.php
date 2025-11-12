<?php
// backend/helpers.php

// Sanitize input for display (prevent XSS)
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Normalize academic year increments.
// If academic_year is "2023-24" -> returns "2024-25"
// If it's a single year "2023" -> returns "2024"
function increment_academic_year($ay) {
    if (preg_match('/^\s*(\d{4})\s*-\s*(\d{2,4})\s*$/', $ay, $m)) {
        // e.g., "2023-24" or "2023-2024"
        $start = intval($m[1]);
        $end = $start + 1;
        // format end with 2 digits if original used 2-digit
        $end_str = (strlen($m[2]) == 2) ? substr(strval($end), -2) : strval($end);
        return $start + 1 . '-' . $end_str;
    } elseif (preg_match('/^\s*(\d{4})\s*$/', $ay, $m)) {
        return strval(intval($m[1]) + 1);
    } else {
        // fallback: append "next"
        return $ay . ' (next)';
    }
}
