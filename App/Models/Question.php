<?php

namespace App\Models;
use App\Models\Model;
use App\Models\Assessment;

class Question extends Model{

    private $data;

    public function __construct($data) {
        $this->data = $data;
    }
    protected static function loadData(){
        return self::loadJsonData('questions.json');
    }



    public static function getQuestionsByStudentResponse($studentResponses){
        $questions = self::loadData();

        foreach ($questions as $question) {
            $questionMap[$question['id']] = $question;
        }
        
        foreach ($studentResponses as $response) {
            $questionId = $response['questionId'];

            if (isset($questionMap[$questionId])) {
                // Merge the response data with the question data
                $mergedItem = array_merge($questionMap[$questionId], ['response' => $response['response']]);
                
                // Add the merged item to the mergedArray
                $mergedArray[] = $mergedItem;
            }
        }

        return $mergedArray;
    }
}