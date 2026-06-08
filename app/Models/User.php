<?php

declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class Comment extends Model
{
    public function __construct(
        public readonly int $id = 0,
        public string       $content = '',
        public string       $created_at = '',
        public int          $article_id = 0,
        public int          $user_id = 0,
        public string       $username = '',
        public string       $article_title = '',
        public string       $article_slug = '',
    )
    {
        parent::__construct(); // On appelle Model pour récupérer $this->pdo
    }

    public static function count(): int
    {
        $instance = new self();
        return (int)$instance->pdo->prepare('SELECT COUNT(*) FROM comments')->fetchColumn();
    }

    public static function find(int $id): array
    {
        $instance = new self();
        $sql = 'SELECT * FROM comments WHERE id = :id';
        $query = $instance->pdo->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByArticles(int $article_id): array
    {
        $instance = new self();
        $sql = 'SELECT comments.*, users.username
          FROM comments
          JOIN users
          ON comments.user_id = users.id
          WHERE article_id= :article_id
          ORDER BY comments.created_at DESC';

        $query = $instance->pdo->prepare($sql);
        $query->execute(compact('article_id'));
        return $query->fetchAll();
    }


    public static function save(string $content, int $article_id, int $user_id): bool
    {
        $instance = new self();

        $query = $instance->pdo->prepare('INSERT INTO comments (content, article_id, user_id, created_at) VALUES (:content, :article_id, :user_id, NOW())');
        return $query->execute(compact('content', 'article_id', 'user_id'));

    }

    /**
     * Récupère tous les commentaires laissés par un utilisateur spécifique
     */
    public static function findByUser(int $user_id): array
    {
        $instance = new self();
        $sql = 'SELECT c.id, c.content, c.created_at, a.id AS article_id, a.title AS article_title, a.slug AS article_slug
            FROM comments c
            LEFT JOIN articles a ON c.article_id = a.id
            WHERE c.user_id = :user_id
            ORDER BY c.created_at DESC';

        $query = $instance->pdo->prepare($sql);
        $query->execute(['user_id' => $user_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un commentaire spécifique par son ID
     */
    public static function findById(int $id): array|false
    {
        $instance = new self();
        $sql = 'SELECT * FROM comments WHERE id = :id';
        $query = $instance->pdo->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime un commentaire
     */
    public static function delete(int $id): bool
    {
        $instance = new self();
        $query = $instance->pdo->prepare('DELETE FROM comments WHERE id = :id');
        return $query->execute(['id' => $id]);
    }

}