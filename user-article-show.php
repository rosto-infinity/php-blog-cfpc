<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

$article_id = $_GET['id'];
$article =findArticle((int)$article_id);

$commentaires = findCommentsByArticles((int) $article_id);

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
