<?php

declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class Article extends Model
{
  public function __construct(
    public readonly int $id = 0,
    public string $title = '',
    public string $slug = '',
    public string $introduction = '',
    public string $content = '',
    public ?string $image = null,
    public string $created_at = '',
    public string $updated_at = '',
    public int $comment_count = 0,
  ) {
    parent::__construct(); // On appelle Model pour récupérer $this->pdo
  }


  public static function count(): int
  {
    $instance = new self();
    return (int) $instance->pdo->prepare('SELECT COUNT(*) FROM articles')->fetchColumn();
  }

 
    /**
     * Récupère un article par son ID.
     * Retourne null si l'article n'existe pas.
     */
    public static function find(int $id): ?self
    {
        $instance = new self();
        $stmt = $instance->pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

  public static function  findAll(
    ?int $limit = null,
    ?int $offset = null,
    string  $searchTerm  = ''
  ): array {
      $instance = new self();
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

    if ($limit !== null  && $offset !== null) {
      $sql .=  '  LIMIT :limit OFFSET :offset';
    }

    $resultats = $instance->pdo->prepare($sql);
    if (!empty($searchTerm)) {
      $resultats->bindValue(':searchTerm', '%' . $searchTerm . '%');
    }
    if ($limit !== null  && $offset !== null) {
      $resultats->bindValue(param: ':limit', value: $limit, type: PDO::PARAM_INT);
      $resultats->bindValue(param: ':offset', value: $offset, type: PDO::PARAM_INT);
    }
    $resultats->execute();
    return  $resultats->fetchAll(PDO::FETCH_ASSOC);
  }

    /**
     * Vérifie si un slug est déjà utilisé.
     */
    public static function slugExists(string $slug): bool
    {
        $instance = new self();
        $stmt = $instance->pdo->prepare('SELECT COUNT(*) FROM articles WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // =========================================================================
    // MÉTHODES D'INSTANCE — Actions sur l'objet courant
    // =========================================================================

    /**
     * Détermine si l'article est nouveau (pas encore en base).
     */
    public function isNew(): bool
    {
        return $this->id === 0;
    }

    /**
     * Sauvegarde l'article en base : INSERT si nouveau, UPDATE sinon.
     */
    public function save(): bool
    {
        if ($this->isNew()) {
            return $this->insert();
        }
        return $this->update();
    }

    /**
     * Supprime l'article de la base de données.
     */
    public function delete(): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    // =========================================================================
    // MÉTHODES PRIVÉES — Logique interne
    // =========================================================================

    private function insert(): bool
    {
        $sql = 'INSERT INTO articles (title, slug, introduction, content, image, created_at, updated_at)
                VALUES (:title, :slug, :introduction, :content, :image, NOW(), NOW())';

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'title'        => $this->title,
            'slug'         => $this->slug,
            'introduction' => $this->introduction,
            'content'      => $this->content,
            'image'        => $this->image,
        ]);
    }

    private function update(): bool
    {
        $sql = 'UPDATE articles SET
                    title        = :title,
                    slug         = :slug,
                    introduction = :introduction,
                    content      = :content,
                    image        = :image,
                    updated_at   = NOW()
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title'        => $this->title,
            'slug'         => $this->slug,
            'introduction' => $this->introduction,
            'content'      => $this->content,
            'image'        => $this->image,
            'id'           => $this->id,
        ]);

        return $stmt->rowCount() > 0;
    }






    /**
     * Fabrique un objet Article depuis un tableau associatif (ligne BDD).
     */
    private static function fromRow(array $row): self
    {
        $article = new self(
            id:            (int) $row['id'],
            title:         $row['title'],
            slug:          $row['slug'],
            introduction:  $row['introduction'],
            content:       $row['content'],
            image:         $row['image'] ?? null,
            created_at:    $row['created_at'] ?? '',
            updated_at:    $row['updated_at'] ?? '',
            comment_count: (int) ($row['comment_count'] ?? 0),
        );

        return $article;
    }
}
