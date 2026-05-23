<?php

declare(strict_types=1);
require_once __DIR__ . '/../../database/database.php';




function findAllArticles( ?int $limit = null , ?int $offset = null , string  $searchTerm  =''): array
{
  $pdo = getPdo();
  $sql = '
    SELECT 
            articles.*,
            (SELECT COUNT(*) FROM comments WHERE comments.article_id = articles.id) AS comment_count
        FROM articles  
     ';
  if (!empty($searchTerm)) {
    $sql .= ' WHERE title LIKE :searchTerm OR introduction LIKE :searchTerm';
  }
 $sql .= ' ORDER BY created_at DESC';

 if ($limit !== null  && $offset !== null ) {
  $sql .=  '  LIMIT :limit OFFSET :offset';
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

 /**
 * Récupère un article spécifique par son ID
 */
function findArticle(int $id): array|false
{
    $pdo = getPdo();
    $sql = 'SELECT * FROM articles WHERE id = :id';
    $query = $pdo->prepare($sql);
    $query->execute(['id' => $id]);
    return $query->fetch(PDO::FETCH_ASSOC);
}
/**
 * Vérifie si un slug existe déjà
 */
function countArticlesBySlug(string $slug): int
{
    $pdo = getPdo();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE slug = :slug');
    $stmt->execute(['slug' => $slug]);
    return (int) $stmt->fetchColumn();
}

/**
 * Ajoute un nouvel article dans la base de données
 */
function insertArticle(string $title, string $slug, string $introduction, string $content, ?string $image): bool
{
    $pdo = getPdo();
    $sql = 'INSERT INTO articles (title, slug, introduction, content, image, created_at, updated_at) 
            VALUES (:title, :slug, :introduction, :content, :image, NOW(), NOW())';
            
    $query = $pdo->prepare($sql);
    return $query->execute([
        'title' => $title,
        'slug' => $slug,
        'introduction' => $introduction,
        'content' => $content,
        'image' => $image,
    ]);
}
/**
 * Met à jour un article existant
 */
function updateArticle(int $id, string $title, string $slug, string $introduction, string $content, ?string $image): bool
{
    $pdo = getPdo();
    $sql = 'UPDATE articles SET 
            title = :title, 
            slug = :slug, 
            introduction = :introduction, 
            content = :content,
            image = :image,
            updated_at = NOW()
            WHERE id = :id';

    $query = $pdo->prepare($sql);
    $query->execute([
        'title' => $title,
        'slug' => $slug,
        'introduction' => $introduction,
        'content' => $content,
        'image' => $image,
        'id' => $id,
    ]);
    
    return $query->rowCount() > 0;
}

/**
 * Supprime un article de la base de données
 */
function deleteArticle(int $id): bool
{
    $pdo = getPdo();
    $query = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    return $query->execute(['id' => $id]);
}

