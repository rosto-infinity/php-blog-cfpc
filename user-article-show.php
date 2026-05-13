<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

$article_id = $_GET['id'];
// var_dump($article_id);
// die;
$sql="SELECT * FROM articles WHERE id = :article_id ";
$query = $pdo->prepare($sql);
$query->execute(compact('article_id'));
$article = $query->fetch();
// var_dump($article);
//  die;
$sql = 'SELECT comments.*, users.username
 FROM comments
 JOIN users ON comments.user_id = users.id
 WHERE article_id= :article_id';
 
$query = $pdo->prepare($sql);
$query->execute(compact('article_id'));
$commentaires = $query->fetchAll();



//Statistiques

  //----------Nombre d'utilisateurs 
  $usersCount = $pdo->query('SELECT COUNT(*) AS count FROM users')->fetch(PDO::FETCH_ASSOC)['count'];
$commentsCount = $pdo->query('SELECT COUNT(*) AS count FROM comments')->fetch(PDO::FETCH_ASSOC)['count'];
  $articlesCount = $pdo->query('SELECT COUNT(*) AS count FROM articles')->fetch(PDO::FETCH_ASSOC)['count'];

$latestArticles = $pdo->query('SELECT * FROM articles ORDER BY created_at DESC LIMIT  5')->fetch(PDO::FETCH_ASSOC);




$pageTitle = 'Affichage d\'un article';
// ob_start();
// require_once 'resources/views/blog/user-article-show_html.php';
// $pageContent = ob_get_clean();
// require_once 'resources/views/layouts/blog-layout/blog-layout_html.php';

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
