<?php

namespace App\Models;

abstract class Model {

    abstract protected static function loadData();

    protected static function loadJsonData($fileName) {
        $json = file_get_contents(__DIR__ . '/../../Datas/' . $fileName);
        return json_decode($json, true);
    }

}