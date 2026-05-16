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


$pageTitle = 'List Articles';
ob_start();
require_once 'resources/views/admin/articles/admin-list-article_html.php';
$pageContent = ob_get_clean();
require_once 'resources/views/layouts/admin-layout/admin-layout_html.php';
