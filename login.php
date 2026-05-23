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
function authenticateUser(string $email, string $password): string {
    if (empty($email) || empty($password)) {
        return "Tous les champs doivent être complétés !";
    }

    $user = findUserByEmailOrUsername($email);

    if (!$user) {
        return "Compte inexistant !";
    }

    if (!password_verify($password, $user['password'])) {
        return "Mauvais mot de passe !";
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

    $result = authenticateUser($email, $password);

    if ($result === "success") {
        flash_set('success', "Heureux de vous revoir " . $_SESSION['auth']['username'] . " !");
        

        // Redirect based on role or to index
        if ($_SESSION['auth']['role'] === Role::ADMIN->value) {
            redirect('admin.php');
        } else {
            redirect('user-dashboard.php');
        }
    } else {
        flash_set('error', $result);
        redirect('login.php');
    }
}




$pageTitle = 'Connexion';
render('users/login', ['pageTitle' => $pageTitle], 'blog-layout');




