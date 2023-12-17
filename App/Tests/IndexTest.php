<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{

    /**
     * @dataProvider inputDataProvider
     */
    public function testProcessInput($studentId, $reportType, $expectedOutput)
    {

        $controller = new \App\Controllers\ReportController();

        ob_start();
        echo $controller->generateReport($studentId, $reportType);
        $output = ob_get_clean();

        // Assert the expected output
        $this->assertStringContainsString($expectedOutput, $output);
    }

    public static function inputDataProvider() {
        return [
            ["student1", "1", "He got 15 questions right out of 16. Details by strand given below"],
            ["student1", "2", "Tony Stark got 9 more correct in the recent completed assessment than the oldest"],
            ["student1", "3", "You must first arrange the numbers in ascending order. The median"]
        ];
    }
}
