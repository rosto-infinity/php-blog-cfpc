<?php
declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';


if ($_SESSION['auth']['role'] !== Role::ADMIN->value) 
  {
    header('Location: index.php');
    exit();
}
// Gestion de la recherche
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = clean_input((string) ($_POST['search'] ?? ''));
}
// Gestion des messages flash de succès
$success = [];
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
$allArticles= findAllArticles(null, null, $searchTerm);

$pageTitle = 'Page Add articles';

// Début du tampon de la page de sortie
render('admin/articles/admin-list-article', [
    'pageTitle' => $pageTitle,
    'allArticles' => $allArticles,
    'success' => $success,
    'searchTerm' => $searchTerm
], 'admin-layout');

