<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';


if (!isset($_SESSION['auth']) || !is_array($_SESSION['auth']) || $_SESSION['auth']['role'] !== Role::ADMIN->value) {
    redirect('index.php');
}
/**
 * Vérifie si l'ID de l'article est passé en GET, valide et existe dans la base de données.
 * Supprime l'article si toutes les vérifications sont réussies et redirige vers la page d'accueil.
 */

// 1. Vérification de l'ID passé en GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    redirect("error.php?message=Id de l'article non valide.");}

// 2. -Vérification que l'article existe
$article = findArticle($id);

if (!$article) {
    redirect("error.php?message=L'article $id n'existe pas, vous ne pouvez donc pas le supprimer !");
}

// 3.- Suppression de l'article
deleteArticle($id);

// 4.- Redirection vers la page d'accueil
redirect('admin-list-article.php');

