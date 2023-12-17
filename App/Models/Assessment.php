<?php

namespace App\Models;
use App\Models\Model;

class Assessment extends Model{

    public $name;

    public function __construct($data) {
        $this->name = $data['name'];
    }
    protected static function loadData(){
        return self::loadJsonData('assessments.json');
    }


    public static function getAssessmentById($assesmentId){
        $datas = self::loadData();
        if (!$datas) {
            return null;
        }
        foreach ($datas as $data) {
            if ($data['id'] == $assesmentId) {
                return new self($data);
            }
        }
    }


}