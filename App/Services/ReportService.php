<?php
namespace App\Services;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Response;
use App\Utils\Utils;

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

        $strandText = '';
        foreach ($strandGroups as $strandName => $strandCounts) {
            $strandText .= $strandName . ': ' . $strandCounts['correct'] . ' out of ' . $strandCounts['total'] . ' correct ' . "\n";
        }
        $lastResponse = $res[0];

        return  $student->getName() . ' recently completed '.$assessment->name.' assessment on ' . Utils::parseTime($lastResponse->completed) . "\n" .
            'He got ' . $lastResponse->results['rawScore'] . ' questions right out of ' . count($lastResponse->responses) . '. ' . 'Details by strand given below: ' . "\n" . "\n" . $strandText;
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

        $scoreTexts = '';
        foreach ($res as $response) {
            $scoreTexts .= 'Date: ' . Utils::praseDate($response->assigned) . ', Raw Score: ' . $response->results['rawScore'] . ' out of ' . count($response->responses) . "\n";
        }

        return $student->getName() . ' has completed ' . $assessment->name . ' assessment ' . count($res) . ' times in total. Date and raw score given below:' . "\n" . "\n" .
            $scoreTexts . "\n" .
            $student->getName() . ' got ' . (end($res)->results['rawScore'] - $res[0]->results['rawScore']) . ' more correct in the recent completed assessment than the oldest';
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

        $questionText = '';
        foreach ($responseWithQuestions as $question) {
            if ($question['response'] == $question['config']['key'] )  { 
                continue;
            }
            $questionText .= 'Question: ' . $question['stem'] . "\n" .
                'Your answer: ' . $this->getLabel($question)  . ' with value ' . $this->getValue($question)  . "\n" .
                'Right answer: ' . $this->getRightLabel($question) . ' with value ' . $this->getRightValue($question) . "\n" .
                'Hint: ' . $question['config']['hint'] . "\n" . "\n";
        }

        return $student->getName() . ' recently completed ' . $assessment->name . ' assessment on ' . Utils::parseTime($res[0]->completed) . "\n" .
            'He got ' . $res[0]->results['rawScore'] . ' questions right out of ' . count($res[0]->responses) . '. Feedback for wrong answers given below' . "\n" . "\n" .
            $questionText;


    }

    private function getLabel($q){

        foreach ($q['config']['options'] as $option){ 
            if ($option['id'] == $q['response']){ 
                return $option['label'];
            }
        }
    }
    private function getValue($q){

        foreach ($q['config']['options'] as $option){ 
            if ($option['id'] == $q['response']){ 
                return $option['value'];
            }
        }
    }
    private function getRightLabel($q){

        foreach ($q['config']['options'] as $option){ 
            if ($option['id'] == $q['config']['key']){ 
                return $option['label'];
            }
        }
    }
    private function getRightValue($q){

        foreach ($q['config']['options'] as $option){ 
            if ($option['id'] == $q['config']['key']){ 
                return $option['value'];
            }
        }
    }
}
