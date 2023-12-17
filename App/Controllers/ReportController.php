<?php

namespace App\Controllers;

use App\Services\ReportService;

class ReportController {
    private $reportService;

    public function __construct() {
        $this->reportService = new ReportService();
    }

    public function generateReport($studentId, $reportType) {
        return $this->reportService->createReport($studentId, $reportType);
    }
}
