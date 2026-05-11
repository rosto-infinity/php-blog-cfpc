<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';

//Requete comptant le totales des articles

$totalQuery = $pdo->prepare('SELECT COUNT(*) FROM articles');
$totalQuery->execute();
$totalItems = (int)$totalQuery->fetchColumn();
// var_dump($totalItems);
// die;

$itemsPerPage = 12; //Nbre d'articles par pages
$currentPage =(int)($_GET['page'] ?? 1 ); //Page actuelle
$totalPages =(int)ceil($totalItems / $itemsPerPage); //Total des pages des articles

$offset =($currentPage -1) * $itemsPerPage;
 //page 1 : (1-1) * 12 = 0   => 1 à 12  
 //page 2  :  (2-1) * 12 = 12   =>  12 à 24   
 //page 3  :  (3-1) * 12 = 24   => 24 à 36
 //.
 //.
 //.
$sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$query = $pdo->prepare($sql);
$query->bindValue(param: ':limit', value : $itemsPerPage, type: PDO::PARAM_INT);
$query->bindValue(param: ':offset', value : $itemsPerPage, type: PDO::PARAM_INT);
$query->execute();
$articles = $query->fetchAll();






















$pageTitle = 'Notre blog d\'accueil';
ob_start();
require_once 'resources/views/blog/index_html.php';
$pageContent = ob_get_clean();
require_once 'resources/views/layouts/blog-layout/blog-layout_html.php';

