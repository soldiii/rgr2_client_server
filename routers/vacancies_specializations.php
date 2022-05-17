<?php
    header ("Access-Control-Allow-Origin: *");
    header ("Content-Type: application/json; charset=UTF-8");
    include_once 'config/database.php';
    include_once 'objects/VacancySpecialization.php';

// Роутер
function route($method, $urlData, $formData) {

    $database = new Database();
    $db = $database->getConnection();
    $vacancy_specialization = new VacancySpecialization($db);
    
    

    // Добавление новой вакансии
    if ($method === 'POST' && empty($urlData)) {
    
        $vacancy_specialization->POSTVacancySpecialization($formData);
        
        return;
    }


    // Обновление всех данных вакансии
    if ($method === 'PUT' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $vacancy_specialization->PUTVacancySpecialization($goodId,$formData);

        return;
    }

    // Удаление вакансии
    if ($method === 'DELETE' && count($urlData) === 1) {
        // Получаем id вакансии
        $goodId = $urlData[0];
        $vacancy_specialization->DELETEVacancySpecialization($goodId);

        return;
    }

}