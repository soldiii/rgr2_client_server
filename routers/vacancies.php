<?php
    header ("Access-Control-Allow-Origin: *");
    header ("Content-Type: application/json; charset=UTF-8");
    include_once 'config/database.php';
    include_once 'objects/Vacancy.php';

// Роутер
function route($method, $urlData, $formData) {

    $database = new Database();
    $db = $database->getConnection();
    $vacancy = new Vacancy($db);

    if($method === 'GET'){

        $begin = $_GET['FromDate'];
        $end = $_GET['ToDate'];
        $begin_sal = $_GET['FromSalary'];
        $end_sal = $_GET['ToSalary'];
        $area = $_GET['Area'];
        $sal_curr = $_GET['SalaryCurrency'];
        $exp = $_GET['Experience'];

        $sort = $_GET['Sort'];

        $page_numb = $_GET['PageNumber'];
        $page_size = $_GET['PageSize'];
        if($page_size == '')
        $page_size=50;


        if($begin == '' && $end != '' || $begin != '' && $end == '' ){
            echo("Неправильно задан промежуток времени");
            return;
        }

        if($begin_sal == '' && $end_sal != '' || $begin_sal != '' && $end_sal == '' ){
            echo("Неправильно задан диапазон зарплат");
            return;
        }

        if(empty($urlData)){
            if($sort!=0){
            $sort_parameters = explode(',',$sort);
            $stmt = $vacancy->WideRequest($begin,$end, $begin_sal,$end_sal, $area, $sal_curr, $exp, $sort_parameters,$page_numb, $page_size);
            }
            else{
                $stmt = $vacancy->WideRequest($begin,$end, $begin_sal,$end_sal, $area, $sal_curr, $exp, $sort_parameters=NULL,$page_numb,$page_size);
            }

        }
            if(count($urlData) === 1){
                $vacancyId = $urlData[0];
                $stmt = $vacancy->VacancyFromID($vacancyId);
            }

            if(count($urlData) === 2 && $urlData[1] == 'skills'){
                $vacancyId = $urlData[0];
                if($sort!=0){
                $sort_parameters = explode(',',$sort);
                $stmt = $vacancy->SkillsForVacancy($vacancyId, $sort_parameters,$page_numb, $page_size);
                }
                else
                $stmt = $vacancy->SkillsForVacancy($vacancyId, $sort_parameters=NULL,$page_numb, $page_size);
                
            }

            if(count($urlData) === 2 && $urlData[0] == 'specializations'){
                $specializationId = $urlData[1];
                if($sort!=0){
                $sort_parameters = explode(',',$sort);
                $stmt = $vacancy->CheckVacanciesForSpecialization($specializationId, $sort_parameters,$page_numb, $page_size);
                }
                else
                $stmt = $vacancy->CheckVacanciesForSpecialization($specializationId, $sort_parameters=NULL,$page_numb, $page_size);
                
            }
        

            $format = $_GET['format'];
            if($format != 'xml' && $format != 'json' && $format != ''){
            echo('Недопустимый параметр для format');
            return;
        }

        $num = $stmt->rowCount();
        if ($num>0) {
            $vacancies_arr=array();
            $vacancies_arr["records"]=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract ($row);
                if($urlData[1] == '')
                $vacancy_item=array(
                    "id_vacancy"=>$id_vacancy,
                    "description"=>$description,
                    "name"=>$name,
                    "published_at"=>$published_at,
                    "salary_currency"=>$salary_currency,
                    "salary_from"=>$salary_from,
                    "salary_to"=>$salary_to,
                    "snippet_requirement"=>$snippet_requirement,
                    "snippet_responsibility"=>$snippet_responsibility,
                    "id_area"=>$id_area,
                    "id_experience"=>$id_experience
                );
                else if($urlData[1] == 'skills')
                $vacancy_item=array(
                    "id_vacancy"=>$id_vacancy,
                    "id_skill"=>$id_skill
                
                );
                else if($urlData[0] == 'specializations')
                $vacancy_item=array(
                    "id_specialization"=>$id_specialization,
                    "specialization_name"=>$specialization_name,
                    "id_vacancy"=> $id_vacancy
                
                );
            

                array_push ($vacancies_arr["records"], $vacancy_item);
            }
                http_response_code (200) ;
                include_once 'config/XmlEncoder.php';
                if($format == 'xml'){
                    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes"?><result/>');
                    to_xml($xml,$vacancies_arr);
                    echo($xml-> asXML());
                    }
                else
                if($format == 'json' || $format == '')
                echo json_encode($vacancies_arr,JSON_UNESCAPED_UNICODE);
        }
            else {
                http_response_code (104) ;
                echo json_encode (array ("message" => "Вакансии не найдены."), JSON_UNESCAPED_UNICODE);
            }

        
    }
    
    // Добавление новой вакансии
    if ($method === 'POST' && empty($urlData)) {
    
        $vacancy->POSTVacancy($formData);
        
        return;
    }


    // Обновление всех данных вакансии
    if ($method === 'PUT' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $vacancy->PUTVacancy($goodId,$formData);

        return;
    }

    // Удаление вакансии
    if ($method === 'DELETE' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $vacancy->DELETEVacancy($goodId);

        return;
    }
}



?>