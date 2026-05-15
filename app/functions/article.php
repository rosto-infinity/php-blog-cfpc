<?php

declare(strict_types=1);
require_once __DIR__ . '/database/database.php';


function countArticles(): int
{
  $pdo = getPdo();
  $query = $pdo->prepare('SELECT COUNT(*) FROM articles');
 return $query->fetchColumn();
}
