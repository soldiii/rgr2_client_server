<?php
class VacancySkill {

private $conn;

// свойства объекта
public $id_vacancy;
public $id_skill;

public function __construct($db) {
	$this->conn = $db;
}

function GettingValues($data){
    $id_vacancy = $data['id_vacancy'];
    $id_skill = $data['id_skill'];
    $array = array($id_vacancy,$id_skill);
    return $array;
}

function bind($stmt,$array_of_values){
    $stmt->bindValue(':id_vacancy', $array_of_values[0],PDO::PARAM_STR);
    $stmt->bindValue(':id_skill', $array_of_values[1],PDO::PARAM_STR);
    return $stmt;
}

function POSTVacancySkill($data){
    $array_of_values=$this->GettingValues($data);

    $query = 'INSERT INTO vacancies_skills (id_vacancy, id_skill) 
    VALUES (:id_vacancy, :id_skill)';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      
      if($stmt->execute())
      echo("Умение для вакансии успешно создано, идентификатор умения - $array_of_values[0]");
     
      return;
  }
  function PUTVacancySkill($goodID,$data){
    $array_of_values=$this->GettingValues($data);
    
    //проверка на наличиие идентификатора в таблице
    $sql = $this->conn->prepare("SELECT COUNT(*) AS `total` FROM vacancies_skills WHERE id_vacancy = :id");
    $sql->execute(array(':id' => $goodID));
    $result = $sql->fetchObject();
    if(!$result->total){
      echo("Умения с таким идентификатором не существует");
      return;
    }

    $query = 'UPDATE vacancies_skills SET id_vacancy = :id_vacancy, id_skill = :id_skill 
    WHERE vacancies_skills.id_vacancy = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Умение успешно изменено, идентификатор умения - $array_of_values[0]");
     
      return;
  }
  function DELETEVacancySkill($goodID){
    $query = 'DELETE FROM vacancies_skills WHERE vacancies_skills.id_vacancy = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Умение успешно удалено, идентификатор удаленного умения - $goodID");
     
      return;
  }
}