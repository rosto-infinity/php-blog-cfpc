<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';



// Vérification des autorisations admin
checkAdmin();

// Récupérer les utilisateurs AVEC leur nombre de commentaires
$users = findUsersWithCommentCount();

// Récupérer les commentaires + infos de l'article pour chaque utilisateur
// Récupérer les utilisateurs AVEC leur nombre de commentaires
$users = User::allWithCommentCount();

$pageTitle = 'Récupérer tous les utilisateurs';
render('admin/users/index-comments', [
    'pageTitle' => $pageTitle,
    'users' => $users
], 'admin-layout');
