<?php

namespace App\Models;
use App\Models\Model;
use App\Models\Assessment;

class StudentResponses extends Model{

    public $completed; 
    public $results;
    public $responses;
    public $assessmentId;
    public $assigned;


    public function __construct($data) {
        $this->completed = isset($data['completed']) ? $data['completed'] : null;
        $this->results = $data['results'];
        $this->responses = $data['responses'];
        $this->assessmentId = $data['assessmentId'];
        $this->assigned = $data['assigned'];
    }

    
    protected static function loadData(){
        return self::loadJsonData('student-responses.json');
    }

    // has many student responses
    public static function getStudentResponsesByStudentId($studentId){

        $datas = self::loadData();

        $responses = [];

        if (!$datas) {
            return $responses;
        }
        foreach ($datas as $data) {
            if ($data['student']['id'] == $studentId) {

                $responses[] = new self($data);
            }
        }
        return $responses;
    }

    public function getStudentResponseAssessments(){
        return Assessment::getAssessmentById($this->assessmentId);
    }

    public function getStudentResponseQuestions(){
        return Question::getQuestionsByStudentResponse($this->responses);
    }



}