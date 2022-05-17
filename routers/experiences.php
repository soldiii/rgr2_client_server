<?php
    header ("Access-Control-Allow-Origin: *");
    header ("Content-Type: application/json; charset=UTF-8");
    include_once 'config/database.php';
    include_once 'objects/Experience.php';

// Роутер
function route($method, $urlData, $formData) {

    $database = new Database();
    $db = $database->getConnection();
    $experience = new Experience($db);

    if($method === 'GET'){
        $ExperienceName = $_GET['ExperienceName'];


        
        if(empty($urlData)){
            $stmt = $experience->IDFromExperienceName($ExperienceName);
        }

        $num = $stmt->rowCount();
        if ($num>0) {
            $experiences_arr=array();
            $experiences_arr["records"]=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract ($row);
                $experience_item=array(
                    "name"=>$name,
                    "id_experience"=>$id_experience
                );
                array_push ($experiences_arr["records"], $experience_item);
            }
                http_response_code (200) ;
                include_once 'config/XmlEncoder.php';
                if($format == 'xml'){
                    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes"?><result/>');
                    to_xml($xml,$experiences_arr);
                    echo($xml-> asXML());
                    }
                else
                if($format == 'json' || $format == '')
                echo json_encode($experiences_arr,JSON_UNESCAPED_UNICODE);
        }
            else {
                http_response_code (104) ;
                echo json_encode (array ("message" => "Вакансии не найдены."), JSON_UNESCAPED_UNICODE);
            }




    }
    
    // Добавление новой вакансии
    if ($method === 'POST' && empty($urlData)) {
    
        $experience->POSTExperience($formData);
        
        return;
    }


    // Обновление всех данных вакансии
    if ($method === 'PUT' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $experience->PUTExperience($goodId,$formData);

        return;
    }

    // Удаление вакансии
    if ($method === 'DELETE' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $experience->DELETEExperience($goodId);

        return;
    }

}