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