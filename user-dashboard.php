<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

// Vérification de l'authentification
if (! isset($_SESSION['auth']) || !is_array($_SESSION['auth'])) {
    redirect('login.php');

}

$pageTitle = 'Page  d\'accueil user';

render('users/user-dashboard',
    [
        'pageTitle' => $pageTitle
    ],
"user-layout");
