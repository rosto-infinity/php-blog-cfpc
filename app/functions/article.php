<?php

declare(strict_types=1);
require_once __DIR__ . '/../../database/database.php';


function countArticles(): int
{
  $pdo = getPdo();
  $query = $pdo->prepare('SELECT COUNT(*) FROM articles');
  return $query->fetchColumn();
}

function findAllArticles( ?int $limit = null , ?int $offset = null , string  $searchTerm  =''): array
{
  $pdo = getPdo();
  $sql = '
    SELECT 
            articles.*,
            (SELECT COUNT(*) FROM comments WHERE comments.article_id = articles.id) AS comment_count
        FROM articles ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
     ';
  if (!empty($searchTerm)) {
    $sql .= ' WHERE title LIKE :searchTerm OR introduction LIKE :searchTerm';
  }

  $resultats = $pdo->prepare($sql);
  if (!empty($searchTerm)) {
    $resultats->bindValue(':searchTerm', '%' . $searchTerm . '%');
  }
  if ($limit !== null  && $offset !== null ) {
       $resultats->bindValue(param: ':limit',value : $limit, type: PDO::PARAM_INT);
      $resultats->bindValue(param: ':offset',value : $offset, type: PDO::PARAM_INT);
  }
  $resultats->execute();
  return  $resultats->fetchAll(PDO::FETCH_ASSOC);
}
