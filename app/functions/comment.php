<?php

declare(strict_types=1);

require_once __DIR__ . '/../../database/database.php';

/**
 * Retourne le nombre total de commentaires
 */
function countComments(): int
{
  $pdo = getPdo();
  $query = $pdo->query('SELECT COUNT(*) FROM comments');
  return (int) $query->fetchColumn();
}

function findCommentsByArticles(int $article_id): array
{
  $pdo = getPdo();
  $sql = 'SELECT comments.*, users.username
          FROM comments
          JOIN users
          ON comments.user_id = users.id
          WHERE article_id= :article_id
          ORDER BY comments.created_at DESC';

  $query = $pdo->prepare($sql);
  $query->execute(compact('article_id'));
  return  $query->fetchAll();
}
