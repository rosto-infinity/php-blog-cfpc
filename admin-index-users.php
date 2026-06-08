<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

// Vérification des autorisations admin
checkAdmin();

try {
     $users = User::findAll();
    $pageTitle = 'Gestion des utilisateurs';

    render('admin/users/index-users', [
        'pageTitle' => $pageTitle,
        'users' => $users
    ], 'admin-layout');

} catch (PDOException $e) {
    exit('Erreur de base de données : '.$e->getMessage());
}
