<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

checkAuth();

$user_id = $_SESSION['auth']['id'];
$comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($comment_id === null || $comment_id === false) {
    exit('ID de commentaire invalide.');
}

// Vérifier si le commentaire existe et appartient à l'utilisateur connecté (ou admin)

$comment = findCommentById((int) $comment_id);

if (! $comment) {
    exit('Commentaire introuvable.');
}

$isAdmin = isset($_SESSION['auth']['role']) && $_SESSION['auth']['role'] === Role::ADMIN->value;
if ($comment['user_id'] !== $user_id && !$isAdmin) {
    exit('Vous ne pouvez pas supprimer ce commentaire.');
}

// -Supprimer le commentaire
deleteComment((int) $comment_id);

redirect('user-article-show.php?id='.$_GET['article_id']);

