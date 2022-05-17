<?php
class Area {

private $conn;

// свойства объекта
public $id_area;
public $name;

public function __construct($db) {
	$this->conn = $db;
}

function GettingValues($data){
    $id = $data['id_area'];
    $name = $data['name'];
    $array = array($id,$name);
    return $array;
}

function bind($stmt,$array_of_values){
    $stmt->bindValue(':id', $array_of_values[0],PDO::PARAM_STR);
    $stmt->bindValue(':name', $array_of_values[1],PDO::PARAM_STR);
    return $stmt;
}

function AreaNameFromID($area_id){
  $query = 'SELECT
    id_area, name
FROM
     area
WHERE
id_area LIKE :area_id'; 

$stmt = $this->conn->prepare($query);
$stmt->bindValue(':area_id', $area_id);
$stmt->execute();

  return $stmt;
}

function IDFromAreaName($areaName){
  $query = 'SELECT
    name, id_area
FROM
     area
WHERE
name LIKE :area_name'; 

$stmt = $this->conn->prepare($query);
$stmt->bindValue(':area_name', $areaName);
$stmt->execute();

  return $stmt;
}

function POSTArea($data){
    $array_of_values=$this->GettingValues($data);

    $query = 'INSERT INTO area (id_area, name) 
    VALUES (:id, :name)';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      
      if($stmt->execute())
      echo("Область успешно создана, идентификатор области - $array_of_values[0]");
     
      return;
  }
  function PUTArea($goodID,$data){
    $array_of_values=$this->GettingValues($data);
    
    //проверка на наличиие идентификатора в таблице
    $sql = $this->conn->prepare("SELECT COUNT(*) AS `total` FROM area WHERE id_area = :id");
    $sql->execute(array(':id' => $goodID));
    $result = $sql->fetchObject();
    if(!$result->total){
      echo("Области с таким идентификатором не существует");
      return;
    }

    $query = 'UPDATE area SET id_area = :id, name = :name
    WHERE area.id_area = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Область успешно изменена, идентификатор области - $array_of_values[0]");
     
      return;
  }
  function DELETEArea($goodID){
    $query = 'DELETE FROM area WHERE area.id_area = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Область успешно удалена, идентификатор удаленной области - $goodID");
     
      return;
  }
}