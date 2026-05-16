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
$sql = "SELECT 
            articles.id,
            articles.title,
            articles.introduction,
            articles.image,
            articles.created_at,
            (SELECT COUNT(*) FROM comments WHERE comments.article_id) AS comment_count
        FROM articles 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset";

$query = $pdo->prepare($sql);
$query->bindValue(param: ':limit', value: $itemsPerPage, type: PDO::PARAM_INT);
$query->bindValue(param: ':offset', value: $itemsPerPage, type: PDO::PARAM_INT);
$query->execute();
$articles = $query->fetchAll();





render('blog/index', [
  'pageTitle' => 'Notre blog d\'accueil',
  'articles' => $articles,
  'totalPages' => $totalPages,
  'currentPage' => $currentPage,
], 'blog-layout'
);