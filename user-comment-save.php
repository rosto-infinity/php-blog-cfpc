<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

// Vérifier si l'utilisateur est connecté
checkAuth();

$user_auth = $_SESSION['auth']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $content = htmlspecialchars($_POST['content']);
    $article_id = $_POST['article_id'];

    // Validation : Vérifier si le champ "content" est vide
    if (empty($content)) {
        $_SESSION['error'] = 'Le champ commentaire est obligatoire.';
        header('Location: user-article-show.php?id='.$article_id);
        exit;
    }

    // Vérification de l'existence de l'article
    if (! findArticle((int)$article_id)) {
        redirect('user-article-show.php?id=' .$article_id);
    }

    // Insertion du commentaire
    insertComment($content,(int) $article_id, (int) $user_auth);

 // Rediriger vers la page de l'article après l'ajout du commentaire
 redirect('user-article-show.php?id=' .$article_id);
}

