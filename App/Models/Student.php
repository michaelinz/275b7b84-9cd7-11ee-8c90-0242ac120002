<?php

namespace App\Models;
use App\Models\Model;
use App\Models\StudentResponses;

class Student extends Model{

    private $id;
    private $firstName;
    private $lastName;
    private $yearLevel;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->yearLevel = $data['yearLevel'];
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->firstName . ' ' . $this->lastName;
    }

    protected static function loadData(){
        return self::loadJsonData('students.json');
    }

    public static function getStudentById($id){
        $datas = self::loadData();
        if (!$datas) {
            return null;
        }
        foreach ($datas as $data) {
            if ($data['id'] == $id) {
                return new self($data);
            }
        }
    }

    public function getStudentStudentResponses(){
        return $responses = StudentResponses::getStudentResponsesByStudentId($this->id);
    }



}