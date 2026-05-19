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


function insertComment(string $content, int $article_id, int $user_id ): bool{
  $pdo = getPdo();

   $query = $pdo->prepare('INSERT INTO comments (content, article_id, user_id, created_at) VALUES (:content, :article_id, :user_id, NOW())');
   return $query->execute(compact('content', 'article_id', 'user_id'));

}
/**
 * Récupère un commentaire spécifique par son ID
 */
function findCommentById(int $id): array|false
{
    $pdo = getPdo();
    $sql = 'SELECT * FROM comments WHERE id = :id';
    $query = $pdo->prepare($sql);
    $query->execute(['id' => $id]);
    return $query->fetch(PDO::FETCH_ASSOC);
}
/**
 * Supprime un commentaire
 */
function deleteComment(int $id): bool
{
    $pdo = getPdo();
    $query = $pdo->prepare('DELETE FROM comments WHERE id = :id');
    return $query->execute(['id' => $id]);
}