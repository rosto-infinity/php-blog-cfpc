<?php

declare(strict_types=1);
session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
 require_once 'app/helpers.php';
// /**
//  * Authenticate a user
//  */
function authenticateUser(PDO $pdo, string $email, string $password): string
{
    if (empty($email) || empty($password)) {
        return "Tous les champs doivent être complétés !";
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return "Idendifiant incorrect !";
    }

    // Set session variables
    $_SESSION['auth'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    return "success";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = strip_tags($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = authenticateUser($pdo, $email, $password);

    if ($result === "success") {
        flash_set('success', "Heureux de vous revoir " . $_SESSION['username'] . " !");

        // Redirect based on role or to index
        if ($_SESSION['auth']['role'] === Role::ADMIN->value) {
            redirect("admin.php");
        } else {
            redirect("index.php");
        }
        exit();
    } else {
        flash_set('error', $result);
      redirect('login.php');
    }
}


$pageTitle = 'Connexion';
render('users/login', compact('pageTitle'), 'blog-layout');
