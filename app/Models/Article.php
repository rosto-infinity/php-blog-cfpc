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

  public static function find(int $id): array
  {
    $instance = new self();
    $sql = 'SELECT * FROM articles WHERE id = :id';
    $query = $instance->pdo->prepare($sql);
    $query->execute(['id' => $id]);
    return  $query->fetch(PDO::FETCH_ASSOC);
   
  }
}
