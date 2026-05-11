<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

// blogprocedural
if ($_SESSION['auth']['role'] !== Role::ADMIN->value) {
  header('Location: index.php');
  exit();
}

$query = $pdo->prepare('DELETE FROM articles WHERE id = :id');
$query->execute(['id'=> $_GET['id']]);

header('Location: admin-list-article.php');
exit();
