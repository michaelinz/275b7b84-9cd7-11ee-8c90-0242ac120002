<?php


require_once __DIR__ . '/vendor/autoload.php';

// Capture CLI arguments (Student ID and Report Type)
echo "Please enter the following:\n";
echo "Student ID: ";
$studentId = trim(fgets(STDIN));
echo "Report to generate (1 for Diagnostic, 2 for Progress, 3 for Feedback): ";
$reportType = trim(fgets(STDIN));

