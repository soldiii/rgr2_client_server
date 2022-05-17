<?php
class Vacancy {

private $conn;

// свойства объекта
public $id_vacancy;
public $description;
public $name;
public $published_at;
public $salary_currency;
public $salary_from;
public $salary_to;
public $snippet_requirement;
public $snippet_responsibility;
public $id_area;
public $id_experience;

public $id_specialization;
public $specialization_name;

public $id_skill;

public function __construct($db) {
	$this->conn = $db;
}
function GettingValues($data){
    $id = $data['id_vacancy'];
    $desc= $data['description'];
    $name = $data['name'];
    $publ_at = $data['published_at'];
    $sal_curr = $data['salary_currency'];
    $sal_from = $data['salary_from'];
    $sal_to = $data['salary_to'];
    $snippet_req = $data['snippet_requirement'];
    $snippet_resp = $data['snippet_responsibility'];
    $id_area = $data['id_area'];
    $id_exp = $data['id_experience'];
    $array = array($id,$desc,$name,$publ_at,$sal_curr,$sal_from,$sal_to,$snippet_req,$snippet_resp,$id_area,$id_exp);
    return $array;
}

function bind($stmt,$array_of_values){
    $stmt->bindValue(':id', $array_of_values[0],PDO::PARAM_STR);
    $stmt->bindValue(':desc', $array_of_values[1],PDO::PARAM_STR);
    $stmt->bindValue(':name', $array_of_values[2],PDO::PARAM_STR);
    $stmt->bindValue(':publ_at', $array_of_values[3],PDO::PARAM_STR);
    $stmt->bindValue(':sal_curr', $array_of_values[4],PDO::PARAM_STR);
    $stmt->bindValue(':sal_from', $array_of_values[5],PDO::PARAM_STR);
    $stmt->bindValue(':sal_to', $array_of_values[6],PDO::PARAM_STR);
    $stmt->bindValue(':snippet_req', $array_of_values[7],PDO::PARAM_STR);
    $stmt->bindValue(':snippet_resp', $array_of_values[8],PDO::PARAM_STR);
    $stmt->bindValue(':id_area', $array_of_values[9],PDO::PARAM_STR);
    $stmt->bindValue(':id_exp', $array_of_values[10],PDO::PARAM_STR);
    return $stmt;
}

function VacancyFromID($vacancyId){
  $query = 'SELECT
    id_vacancy, description, name, published_at, salary_currency, salary_from, salary_to, snippet_requirement, snippet_responsibility, id_area, id_experience
FROM
     vacancies 
WHERE
    id_vacancy LIKE :id'; 

$stmt = $this->conn->prepare($query);
$stmt->bindValue(':id', $vacancyId);
$stmt->execute();

return $stmt;
}

function SORT($query,$sort_parameters){
  $query .= ' ORDER BY ';
  for ($i = 0; $i < sizeof($sort_parameters); $i++){
    $symbol = $sort_parameters[$i][0];
    if($symbol != '-' && $i != sizeof($sort_parameters) - 1)
    $query.= "{$sort_parameters[$i]}, ";
    else if($symbol != '-' && $i == sizeof($sort_parameters) - 1)
    $query.= "{$sort_parameters[$i]}";
    else if($symbol == '-' && $i != sizeof($sort_parameters) - 1){
      $str = str_replace('-','',$sort_parameters[$i]);
    $query.= "{$str} DESC, ";
    }
    else if($symbol == '-' && $i == sizeof($sort_parameters) - 1){
    $str = str_replace('-','',$sort_parameters[$i]);
    $query.= "{$str} DESC";
    }

}
return $query;
}

function Pagination($page_numb,$page_size,$query){
$query .= ' LIMIT ';
if($page_numb== '' && $page_size !='')
$query.= ":page_size";
if($page_numb!=''&& $page_size==''){
$query.= "50";
$query.= " OFFSET ";
$result = $page_numb*50 - 1;
$query.= "{$result}";
}
if($page_numb!=''&& $page_size!=''){
  $query.= ":page_size";
  $query.= " OFFSET ";
  $result = $page_numb*$page_size - 1;
  $query.= "{$result}";
  }

return $query;
}

function SkillsForVacancy($vacancyId, $sort_parameters,$page_numb, $page_size){
  $query = 'SELECT 
    vacancies.id_vacancy as id_vacancy, vacancies_skills.id_skill as id_skill
FROM
    vacancies_skills INNER JOIN vacancies ON vacancies_skills.id_vacancy = vacancies.id_vacancy
WHERE
    vacancies.id_vacancy LIKE :id';

if($sort_parameters!=NULL)
$query= $this->SORT($query,$sort_parameters);

if($page_size!='' || $page_numb!='')
$query = $this->Pagination($page_numb, $page_size,$query);

$stmt = $this->conn->prepare($query);
$stmt->bindValue(':id', $vacancyId);
$stmt->bindValue(':page_size', $page_size,PDO::PARAM_INT);
$stmt->execute();
return $stmt;
}

function CheckVacanciesForSpecialization($specializationId, $sort_parameters,$page_numb, $page_size){
  $query = 'SELECT 
    specializations.id_specialization as id_specialization, specializations.name as specialization_name, vacancies.id_vacancy as id_vacancy
FROM
    vacancies INNER JOIN vacancies_specializations ON vacancies.id_vacancy = vacancies_specializations.id_vacancy INNER JOIN specializations ON specializations.id_specialization = vacancies_specializations.id_specialization 
WHERE
specializations.id_specialization LIKE :id';

if($sort_parameters!=NULL)
$query= $this->SORT($query,$sort_parameters);

if($page_size!='' || $page_numb!='')
$query = $this->Pagination($page_numb, $page_size,$query);

$stmt = $this->conn->prepare($query);
$stmt->bindValue(':id', $specializationId,PDO::PARAM_STR);
$stmt->bindValue(':page_size', $page_size,PDO::PARAM_INT);
$stmt->execute();
return $stmt;
}

function WideRequest($begin,$end, $begin_sal,$end_sal, $area, $sal_curr, $exp, $sort_parameters, $page_numb, $page_size){
  if($begin_sal == '') $begin_sal='0';
  if($end_sal == '') $end_sal='1000000';

  $query = 'SELECT 
  id_vacancy, description, name, published_at, salary_currency, salary_from, salary_to, snippet_requirement, snippet_responsibility, id_area, id_experience
FROM
  vacancies
WHERE
  vacancies.published_at >= :begin AND vacancies.published_at <= :end AND vacancies.salary_from >= :begin_sal AND vacancies.salary_to <= :end_sal AND id_area LIKE :area AND id_experience LIKE :id_exp AND salary_currency LIKE :sal_curr';

if($sort_parameters!=NULL)
$query= $this->SORT($query,$sort_parameters);

if($page_size!='' || $page_numb!='')
$query = $this->Pagination($page_numb, $page_size,$query);

if($begin == '') $query = str_replace('vacancies.published_at >= :begin AND vacancies.published_at <= :end AND','',$query);
if($area == '') $query = str_replace('AND id_area LIKE :area','',$query);
if($exp == '') $query = str_replace('AND id_experience LIKE :id_exp','',$query);
if($sal_curr == '') $query = str_replace('AND salary_currency LIKE :sal_curr','',$query);

$stmt = $this->conn->prepare($query);
if($begin != ''){
$stmt->bindValue(':begin', $begin,PDO::PARAM_STR);
$stmt->bindValue(':end', $end,PDO::PARAM_STR);
}
$stmt->bindValue(':begin_sal', $begin_sal,PDO::PARAM_STR);
$stmt->bindValue(':end_sal', $end_sal,PDO::PARAM_STR);
if($area != '')
$stmt->bindValue(':area', $area,PDO::PARAM_STR);
if($exp != '')
$stmt->bindValue(':id_exp', $exp,PDO::PARAM_STR);
if($sal_curr != '')
$stmt->bindValue(':sal_curr', $sal_curr,PDO::PARAM_STR);

$stmt->bindValue(':page_size', $page_size,PDO::PARAM_INT);

$stmt->execute();

return $stmt;
}

function POSTVacancy($data){
    $array_of_values=$this->GettingValues($data);

    $query = 'INSERT INTO vacancies (id_vacancy, description, name, published_at, salary_currency, salary_from, salary_to, snippet_requirement, 
    snippet_responsibility, id_area, id_experience) 
    VALUES (:id, :desc, :name, :publ_at, :sal_curr, :sal_from, :sal_to, :snippet_req, :snippet_resp, :id_area, :id_exp)';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      
      if($stmt->execute())
      echo("Вакансия успешно создана, идентификатор вакансии - $array_of_values[0]");
     
      return;
  }
  function PUTVacancy($goodID,$data){
    $array_of_values=$this->GettingValues($data);
    
    //проверка на наличиие идентификатора в таблице
    $sql = $this->conn->prepare("SELECT COUNT(*) AS `total` FROM vacancies WHERE id_vacancy = :id");
    $sql->execute(array(':id' => $goodID));
    $result = $sql->fetchObject();
    if(!$result->total){
      echo("Вакансии с таким идентификатором не существует");
      return;
    }

    $query = 'UPDATE vacancies SET id_vacancy = :id, description = :desc, name = :name, published_at = :publ_at, salary_currency = :sal_curr, 
    salary_from = :sal_from, salary_to = :sal_to, snippet_requirement = :snippet_req, snippet_responsibility = :snippet_resp, id_area = :id_area, id_experience = :id_exp 
    WHERE vacancies.id_vacancy = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt = $this->bind($stmt,$array_of_values);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Вакансия успешно изменена, идентификатор вакансии - $array_of_values[0]");
     
      return;
  }
  function DELETEVacancy($goodID){
    $query = 'DELETE FROM vacancies WHERE vacancies.id_vacancy = :goodID';  
      
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':goodID', $goodID);

      if($stmt->execute())
      echo("Вакансия успешно удалена, идентификатор удаленной вакансии - $goodID");
     
      return;
  }


}