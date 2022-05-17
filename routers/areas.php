<?php
    header ("Access-Control-Allow-Origin: *");
    header ("Content-Type: application/json; charset=UTF-8");
    include_once 'config/database.php';
    include_once 'objects/Area.php';

// Роутер
function route($method, $urlData, $formData) {

    $database = new Database();
    $db = $database->getConnection();
    $area = new Area($db);
    
    if($method === 'GET'){
        $areaName = $_GET['AreaName'];

        if(count($urlData) === 1){
            $area_id = $urlData[0];
            $stmt = $area->AreaNameFromID($area_id);

        }
        if(empty($urlData)){
            $stmt = $area->IDFromAreaName($areaName);
        }

        $num = $stmt->rowCount();
        if ($num>0) {
            $areas_arr=array();
            $areas_arr["records"]=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract ($row);
                if($area_id!='')
                $area_item=array(
                    "id_area"=>$id_area,
                    "name"=>$name
                );
                else
                $area_item=array(
                    "name"=>$name,
                    "id_area"=>$id_area
                );
                array_push ($areas_arr["records"], $area_item);
            }
                http_response_code (200) ;
                include_once 'config/XmlEncoder.php';
                if($format == 'xml'){
                    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes"?><result/>');
                    to_xml($xml,$areas_arr);
                    echo($xml-> asXML());
                    }
                else
                if($format == 'json' || $format == '')
                echo json_encode($areas_arr,JSON_UNESCAPED_UNICODE);
        }
            else {
                http_response_code (104) ;
                echo json_encode (array ("message" => "Вакансии не найдены."), JSON_UNESCAPED_UNICODE);
            }




    }
    // Добавление новой вакансии
    if ($method === 'POST' && empty($urlData)) {
    
        $area->POSTArea($formData);
        
        return;
    }


    // Обновление всех данных вакансии
    if ($method === 'PUT' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $area->PUTArea($goodId,$formData);

        return;
    }

    // Удаление вакансии
    if ($method === 'DELETE' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $area->DELETEArea($goodId);

        return;
    }

}