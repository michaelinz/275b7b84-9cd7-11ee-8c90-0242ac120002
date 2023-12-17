<?php
namespace App\Services;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Response;
use App\Utils\Utils;
use App\Resources\Resource;

class ReportService
{
    private $dataManager;

    private $student;

    public function __construct()
    {
    }

    public function createReport($studentId, $reportType)
    {
        $student = Student::getStudentById($studentId);

        if (!$student instanceof Student) {
            return "Student not found.";
        }

        switch ($reportType) {
            case '1':
                return $this->createDiagnosticReport($student);
            case '2':
                return $this->createProgressReport($student);
            case '3':
                return $this->createFeedbackReport($student);
            default:
                return "Invalid report type.";
        }
    }

    private function createDiagnosticReport(Student $student)
    {
        $res = $student->getStudentStudentResponses();

        usort($res, function ($a, $b) {
            $dateA = isset($a->completed) ? \DateTime::createFromFormat('d/m/Y H:i:s', $a->completed)->getTimestamp() : 0;
            $dateB = isset($b->completed) ? \DateTime::createFromFormat('d/m/Y H:i:s', $b->completed)->getTimestamp() : 0;

            return $dateB - $dateA; 
        });

        $responseWithQuestions = $res[0]->getStudentResponseQuestions();

        $assessment = $res[0]->getStudentResponseAssessments();

        // group by strand
        $strandGroups = [];

        foreach ($responseWithQuestions as $question) {
            $strand = $question['strand']; 

            // Check if the strand is already accounted for
            if (isset($strandGroups[$strand])) {
                // Increment the count for this strand
                $strandGroups[$strand]['total']++;
                if ($question['response'] == $question['config']['key'] )  { 
                    $strandGroups[$strand]['correct']++;
                } 
            } else {
                // Initialize the count for this strand
                $strandGroups[$strand]['total'] = 1;
                if ($question['response'] == $question['config']['key'] )  { 
                    $strandGroups[$strand]['correct'] = 1;
                }  else { 
                    $strandGroups[$strand]['correct'] = 0;
                }
            }
        }

        return Resource::DiagnosticReportResource(student: $student, assessment: $assessment, lastStudentResponse: $res[0], questionByStrands: $strandGroups);
    }

    private function createProgressReport(Student $student)
    {
        $res = $student->getStudentStudentResponses();

        $res = array_filter($res, function ($item) {
            return isset($item->completed);
        });

        usort($res, function ($a, $b) {
            $dateA = isset($a->completed) ? \DateTime::createFromFormat('d/m/Y H:i:s', $a->completed)->getTimestamp() : 0;
            $dateB = isset($b->completed) ? \DateTime::createFromFormat('d/m/Y H:i:s', $b->completed)->getTimestamp() : 0;

            return $dateA - $dateB ; 
        });

        $assessment = $res[0]->getStudentResponseAssessments();

        return Resource::ProgressReportResource(student:$student, assessment:$assessment, studentResponses: $res);
    }

    private function createFeedbackReport(Student $student)
    {
        $res = $student->getStudentStudentResponses();

        usort($res, function ($a, $b) {
            $dateA = isset($a->completed) ? \DateTime::createFromFormat('d/m/Y H:i:s', $a->completed)->getTimestamp() : 0;
            $dateB = isset($b->completed) ? \DateTime::createFromFormat('d/m/Y H:i:s', $b->completed)->getTimestamp() : 0;

            return $dateB - $dateA ;
        });


        $responseWithQuestions = $res[0]->getStudentResponseQuestions();

        $assessment = $res[0]->getStudentResponseAssessments();

        return Resource::FeedbackReportResource(student:$student, assessment:$assessment, studentResponses: $res, lastResponseWithQuestions: $responseWithQuestions);
    }

}
