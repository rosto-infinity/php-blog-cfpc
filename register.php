<?php
declare(strict_types=1);
session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

// Rediriger si déjà connecté
if (isset($_SESSION['id'])) {
    redirect("profil.php?id={$_SESSION['id']}");
}

/**
 * Inscrit un nouvel utilisateur en base de données.
 * Retourne "success" ou un message d'erreur.
 */
function register(string $username, string $email, string $password, string $confirm_password): string
{
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        return "Tous les champs doivent être remplis.";
    }
    if (strlen($username) > 255) return "Votre pseudo ne doit pas dépasser 255 caractères.";

    if (findUserByUsername($username)) return "Ce pseudo est déjà utilisé.";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Adresse email invalide.";

    if (findUserByEmail($email)) return "Adresse mail déjà utilisée !";

    if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password)) {
        return "Mot de passe : 8 caractères min. avec une lettre et un chiffre.";
    }
    if ($password !== $confirm_password) return "Les mots de passe ne correspondent pas !";

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    if (insertUser($username, $email, $hashedPassword)) {
        return "success";
    }

    return "Erreur lors de l'inscription.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = strip_tags($_POST['username'] ?? '');
    $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $result = register($username, $email, $password, $confirm_password);

    if ($result === "success") {
        flash_set('success', "Compte créé avec succès ! Vous pouvez maintenant vous connecter.");
        redirect('login.php');
    }

    flash_set('error', $result);
    redirect('register.php');
}


$pageTitle = 'S\'incrire';
render('users/register', ['pageTitle' => $pageTitle], 'blog-layout');
