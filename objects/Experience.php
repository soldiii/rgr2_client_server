<?php
class Experience {

private $conn;

// свойства объекта
public $id_experience;
public $name;

public function __construct($db) {
	$this->conn = $db;
}

function GettingValues($data){
    $id = $data['id_experience'];
    $name = $data['name'];
    $array = array($id,$name);
    return $array;
}

function bind($stmt,$array_of_values){
    $stmt->bindValue(':id', $array_of_values[0],PDO::PARAM_STR);
    $stmt->bindValue(':name', $array_of_values[1],PDO::PARAM_STR);
    return $stmt;
}

function IDFromExperienceName($ExperienceName){
  $query = 'SELECT
    name, id_experience
FROM
     experience
WHERE
name LIKE :exp_name'; 

$stmt = $this->conn->prepare($query);
$stmt->bindValue(':exp_name', $ExperienceName);
$stmt->execute();

  return $stmt;
}

function POSTExperience($data){
    $array_of_values=$this->GettingValues($data);

    $query = 'INSERT INTO experience (id_experience, name) 
    VALUES (:id, :name)';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      
      if($stmt->execute())
      echo("Опыт работы успешно создан, идентификатор опыта - $array_of_values[0]");
     
      return;
  }
  function PUTExperience($goodID,$data){
    $array_of_values=$this->GettingValues($data);
    
    //проверка на наличиие идентификатора в таблице
    $sql = $this->conn->prepare("SELECT COUNT(*) AS `total` FROM experience WHERE id_experience = :id");
    $sql->execute(array(':id' => $goodID));
    $result = $sql->fetchObject();
    if(!$result->total){
      echo("Опыта работы с таким идентификатором не существует");
      return;
    }

    $query = 'UPDATE experience SET id_experience = :id, name = :name
    WHERE experience.id_experience = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Опыт работы успешно изменен, идентификатор опыта - $array_of_values[0]");
     
      return;
  }
  function DELETEExperience($goodID){
    $query = 'DELETE FROM experience WHERE experience.id_experience = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Опыт работы успешно удален, идентификатор удаленного опыта - $goodID");
     
      return;
  }
}