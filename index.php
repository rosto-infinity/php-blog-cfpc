<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
 require_once 'app/helpers.php';

//Requete comptant le totales des articles

$totalQuery = $pdo->prepare('SELECT COUNT(*) FROM articles');
$totalQuery->execute();
$totalItems = (int)$totalQuery->fetchColumn();
// var_dump($totalItems);
// die;

$itemsPerPage = 12; //Nbre d'articles par pages
$currentPage = (int)($_GET['page'] ?? 1); //Page actuelle
$totalPages = (int)ceil($totalItems / $itemsPerPage); //Total des pages des articles

$offset = ($currentPage - 1) * $itemsPerPage;

$articles = findAllArticles($itemsPerPage,$offset);


render('blog/index', [
  'pageTitle' => 'Notre blog d\'accueil',
  'articles' => $articles,
  'totalPages' => $totalPages,
  'currentPage' => $currentPage,
], 'blog-layout'
);