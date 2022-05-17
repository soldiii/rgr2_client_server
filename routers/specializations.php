<?php
    header ("Access-Control-Allow-Origin: *");
    header ("Content-Type: application/json; charset=UTF-8");
    include_once 'config/database.php';
    include_once 'objects/Specialization.php';

// Роутер
function route($method, $urlData, $formData) {

    $database = new Database();
    $db = $database->getConnection();
    $specialization = new Specialization($db);

    if($method === 'GET'){
        $spec_name = $_GET['SpecializationName'];

        if(empty($urlData)){
            $stmt = $specialization->IDFromSpecializationName($spec_name);
        }

        $num = $stmt->rowCount();
        if ($num>0) {
            $specializations_arr=array();
            $specializations_arr["records"]=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract ($row);
                $specialization_item=array(
                    "id_specialization"=>$id_specialization,
                    "name"=>$name
                );

                array_push ($specializations_arr["records"], $specialization_item);
            }
                http_response_code (200) ;
                include_once 'config/XmlEncoder.php';
                if($format == 'xml'){
                    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes"?><result/>');
                    to_xml($xml,$specializations_arr);
                    echo($xml-> asXML());
                    }
                else
                if($format == 'json' || $format == '')
                echo json_encode($specializations_arr,JSON_UNESCAPED_UNICODE);
        }
            else {
                http_response_code (104) ;
                echo json_encode (array ("message" => "Вакансии не найдены."), JSON_UNESCAPED_UNICODE);
            }




    }
    
    // Добавление новой вакансии
    if ($method === 'POST' && empty($urlData)) {
    
        $specialization->POSTSpecialization($formData);
        
        return;
    }


    // Обновление всех данных вакансии
    if ($method === 'PUT' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $specialization->PUTSpecialization($goodId,$formData);

        return;
    }

    // Удаление вакансии
    if ($method === 'DELETE' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $specialization->DELETESpecialization($goodId);

        return;
    }

}