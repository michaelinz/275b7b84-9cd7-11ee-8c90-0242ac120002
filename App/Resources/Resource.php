<?php

namespace App\Resources;

use App\Utils\Utils;


class Resource
{
    public static function DiagnosticReportResource($student, $assessment, $lastStudentResponse, $questionByStrands)
    {
        $strandText = '';

        foreach ($questionByStrands as $strandName => $strandCounts) {
            $strandText .= $strandName . ': ' . $strandCounts['correct'] . ' out of ' . $strandCounts['total'] . ' correct ' . "\n";
        }

        return $student->getName() . ' recently completed ' . $assessment->name . ' assessment on ' . Utils::parseTime($lastStudentResponse->completed) . "\n" .
            'He got ' . $lastStudentResponse->results['rawScore'] . ' questions right out of ' . count($lastStudentResponse->responses) . '. ' . 'Details by strand given below: ' . "\n" . "\n" .
            $strandText;
    }

    public static function ProgressReportResource($student, $assessment, $studentResponses)
    {
        $scoreTexts = '';
        foreach ($studentResponses as $response) {
            $scoreTexts .= 'Date: ' . Utils::praseDate($response->assigned) . ', Raw Score: ' . $response->results['rawScore'] . ' out of ' . count($response->responses) . "\n";
        }

        return $student->getName() . ' has completed ' . $assessment->name . ' assessment ' . count($studentResponses) . ' times in total. Date and raw score given below:' . "\n" . "\n" .
            $scoreTexts . "\n" .
            $student->getName() . ' got ' . (end($studentResponses)->results['rawScore'] - $studentResponses[0]->results['rawScore']) . ' more correct in the recent completed assessment than the oldest';
    }

    public static function FeedbackReportResource($student, $assessment, $studentResponses, $lastResponseWithQuestions)
    {
        $questionText = '';
        foreach ($lastResponseWithQuestions as $question) {
            if ($question['response'] == $question['config']['key']) {
                continue;
            }
            $questionText .= 'Question: ' . $question['stem'] . "\n" .
                'Your answer: ' . self::getLabel($question) . ' with value ' . self::getValue($question) . "\n" .
                'Right answer: ' . self::getRightLabel($question) . ' with value ' . self::getRightValue($question) . "\n" .
                'Hint: ' . $question['config']['hint'] . "\n" . "\n";
        }

        return $student->getName() . ' recently completed ' . $assessment->name . ' assessment on ' . Utils::parseTime($studentResponses[0]->completed) . "\n" .
            'He got ' . $studentResponses[0]->results['rawScore'] . ' questions right out of ' . count($studentResponses[0]->responses) . '. Feedback for wrong answers given below' . "\n" . "\n" .
            $questionText;

    }

    private static function getLabel($q)
    {

        foreach ($q['config']['options'] as $option) {
            if ($option['id'] == $q['response']) {
                return $option['label'];
            }
        }
    }
    private static function getValue($q)
    {

        foreach ($q['config']['options'] as $option) {
            if ($option['id'] == $q['response']) {
                return $option['value'];
            }
        }
    }
    private static function getRightLabel($q)
    {

        foreach ($q['config']['options'] as $option) {
            if ($option['id'] == $q['config']['key']) {
                return $option['label'];
            }
        }
    }
    private static function getRightValue($q)
    {

        foreach ($q['config']['options'] as $option) {
            if ($option['id'] == $q['config']['key']) {
                return $option['value'];
            }
        }
    }

}