<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

$article_id = $_GET['id'];
$article =findArticle((int)$article_id);

$sql = 'SELECT comments.*, users.username
 FROM comments
 JOIN users ON comments.user_id = users.id
 WHERE article_id= :article_id';
 
$query = $pdo->prepare($sql);
$query->execute(compact('article_id'));
$commentaires = $query->fetchAll();



//Statistiques
  $usersCount = countUsers();
  $commentsCount = countComments();
  $articlesCount = countArticles();

  $latestArticles = findAllArticles(5,0);

$pageTitle = 'Affichage d\'un article';
render('blog/user-article-show', [
  'pageTitle' => $pageTitle,
  'article' => $article,
  'article_id' => $article_id,
  'commentaires' => $commentaires,
   'usersCount' => $usersCount,
    'commentsCount' => $commentsCount,
    'articlesCount' => $articlesCount,
    'latestArticles' => $latestArticles
], 'blog-layout'
);
