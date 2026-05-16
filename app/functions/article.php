<?php

declare(strict_types=1);
require_once __DIR__ . '/database/database.php';


function countArticles(): int
{
  $pdo = getPdo();
  $query = $pdo->prepare('SELECT COUNT(*) FROM articles');
 return $query->fetchColumn();
}

function findAllArticles(){
 $pdo = getPdo();
  $sql ='';
  
$searchTerm = '';
if(isset($_POST['search'])) {
  $searchTerm =clean_input((string) ($_POST['search'] ?? ''));
  }

$query = 'SELECT * FROM articles';
if(!empty($searchTerm)){
  $query .=' WHERE title LIKE :searchTerm OR introduction LIKE :searchTerm';
}
$query .= ' ORDER BY created_at DESC';
 $resultats= $pdo->prepare($query);
 if(!empty($searchTerm)){
$resultats->bindValue(':searchTerm', '%' .$searchTerm. '%');
 }
$resultats->execute();
 $allArticles = $resultats->fetchAll(PDO::FETCH_ASSOC);

$success = [];
$flash = flash_get();
if ($flash !== null) {
    $success['update'] = $flash['message'];
}
}