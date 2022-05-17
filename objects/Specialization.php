<?php
class Specialization {

private $conn;

// свойства объекта
public $id_specialization;
public $name;

public function __construct($db) {
	$this->conn = $db;
}

function GettingValues($data){
    $id = $data['id_specialization'];
    $name = $data['name'];
    $array = array($id,$name);
    return $array;
}

function bind($stmt,$array_of_values){
    $stmt->bindValue(':id', $array_of_values[0],PDO::PARAM_STR);
    $stmt->bindValue(':name', $array_of_values[1],PDO::PARAM_STR);
    return $stmt;
}

function IDFromSpecializationName($spec_name){
  $query = 'SELECT
    name, id_specialization
FROM
     specializations
WHERE
name LIKE :spec_name'; 

$stmt = $this->conn->prepare($query);
$stmt->bindValue(':spec_name', $spec_name);
$stmt->execute();

  return $stmt;
}

function POSTSpecialization($data){
    $array_of_values=$this->GettingValues($data);

    $query = 'INSERT INTO specializations (id_specialization, name) 
    VALUES (:id, :name)';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      
      if($stmt->execute())
      echo("Специализация успешно создана, идентификатор специализации - $array_of_values[0]");
     
      return;
  }
  function PUTSpecialization($goodID,$data){
    $array_of_values=$this->GettingValues($data);
    
    //проверка на наличиие идентификатора в таблице
    $sql = $this->conn->prepare("SELECT COUNT(*) AS `total` FROM specializations WHERE id_specialization = :id");
    $sql->execute(array(':id' => $goodID));
    $result = $sql->fetchObject();
    if(!$result->total){
      echo("Специализации с таким идентификатором не существует");
      return;
    }

    $query = 'UPDATE specializations SET id_specialization = :id, name = :name
    WHERE specializations.id_specialization = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Специализация успешно изменена, идентификатор специализации - $array_of_values[0]");
     
      return;
  }
  function DELETESpecialization($goodID){
    $query = 'DELETE FROM specializations WHERE specializations.id_specialization = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Специализация успешно удалена, идентификатор удаленной специализации - $goodID");
     
      return;
  }
}