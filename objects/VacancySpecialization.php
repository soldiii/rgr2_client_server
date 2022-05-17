<?php
class VacancySpecialization {

private $conn;

// свойства объекта
public $id_vacancy;
public $id_specialization;

public function __construct($db) {
	$this->conn = $db;
}


function GettingValues($data){
    $id = $data['id_vacancy'];
    $name = $data['id_specialization'];
    $array = array($id,$name);
    return $array;
}

function bind($stmt,$array_of_values){
    $stmt->bindValue(':id_vacancy', $array_of_values[0],PDO::PARAM_STR);
    $stmt->bindValue(':id_specialization', $array_of_values[1],PDO::PARAM_STR);
    return $stmt;
}

function POSTVacancySpecialization($data){
    $array_of_values=$this->GettingValues($data);

    $query = 'INSERT INTO vacancies_specializations (id_vacancy, id_specialization) 
    VALUES (:id_vacancy, :id_specialization)';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      
      if($stmt->execute())
      echo("Специализация успешно создана, идентификатор специализации - $array_of_values[0]");
     
      return;
  }
  function PUTVacancySpecialization($goodID,$data){
    $array_of_values=$this->GettingValues($data);
    
    //проверка на наличиие идентификатора в таблице
    $sql = $this->conn->prepare("SELECT COUNT(*) AS `total` FROM vacancies_specializations WHERE id_vacancy = :id");
    $sql->execute(array(':id' => $goodID));
    $result = $sql->fetchObject();
    if(!$result->total){
      echo("Специализации с таким идентификатором не существует");
      return;
    }

    $query = 'UPDATE vacancies_specializations SET id_vacancy = :id_vacancy, id_specialization = :id_specialization
    WHERE vacancies_specializations.id_vacancy = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      $stmt->bindValue(':goodID', $goodID);
      if($stmt->execute())
      echo("Специализация успешно изменена, идентификатор специализации - $array_of_values[0]");
     
      return;
  }
  function DELETEVacancySpecialization($goodID){
    $query = 'DELETE FROM vacancies_specializations WHERE vacancies_specializations.id_vacancy = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Специализация успешно удалена, идентификатор удаленной специализации - $goodID");
     
      return;
  }
}