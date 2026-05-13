<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

// Vérification des autorisations admin

if ($_SESSION['auth']['role'] !== Role::ADMIN->value) {
    header('Location: index.php');
    exit();
}

// Récupérer les utilisateurs AVEC leur nombre de commentaires
$usersQuery = $pdo->query('
    SELECT u.id, u.username, COUNT(c.id) AS comment_count
    FROM users u
    LEFT JOIN comments c ON u.id = c.user_id
    GROUP BY u.id
');
$users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les commentaires + infos de l'article pour chaque utilisateur
foreach ($users as &$user) {
    $commentsQuery = $pdo->prepare('
        SELECT c.id, c.content, c.created_at, a.id AS article_id, a.title AS article_title, a.slug AS article_slug
        FROM comments c
        LEFT JOIN articles a ON c.article_id = a.id
        WHERE c.user_id = :user_id
    ');
    $commentsQuery->execute(['user_id' => $user['id']]);
    $user['comments'] = $commentsQuery->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = 'Récupérer tous les utilisateurs';
ob_start();
include 'resources/views/admin/users/index-comments_html.php';
$pageContent = ob_get_clean();
include 'resources/views/layouts/admin-layout/admin-layout_html.php';
