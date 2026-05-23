<?php

declare(strict_types=1);
session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';

if ($_SESSION['auth']['role'] !== Role::ADMIN->value) {
  header('Location: index.php');
  exit();
}

$messages = [
    'errors' => [],
    'success' => [],
];

$article = []; // Initialisation de la variable article
$currentImage = null; // Initialisation explicite

/**
 * Éditer un article existant
 */

// Récupération des informations d'un article à modifier
if (isset($_GET['id'])) {
    $articleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
     $article = findArticle($articleId);

    // Récupération des données APRÈS la requête
    $title = $article['title'] ?? '';
    $slug = $article['slug'] ?? '';
    $introduction = $article['introduction'] ?? '';
    $content = $article['content'] ?? '';
    $currentImage = $article['image'] ?? null; // Utilisez 'image' ou 'image' selon votre BDD
}

// Traitement de la soumission du formulaire
if (isset($_POST['update'])) {
    // Récupération de l'ID et nettoyage
    $articleId = clean_input((string) $_POST['id']);

    // ---- Nettoyage des entrées
    $title = clean_input((string) filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW));
    $slug = strtolower(str_replace(' ', '-', $title)); // Mise à jour du slug à partir du titre
    $introduction = clean_input((string) filter_input(INPUT_POST, 'introduction', FILTER_UNSAFE_RAW));
    $content = $_POST['content'] ?? '';

    // Traitement de l'image uploadée
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $path = 'storage/articles/';

        $errorMsg = match(true) {
            !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => "Format d'image non supporté.",
            $file['size'] > 2097152 => "Fichier trop lourd (limite : 2 Mo).",
            default => null
        };

        if ($errorMsg) {
            $messages['errors'][] = $errorMsg;
        } else {
            if (!is_dir($path)) mkdir($path, 0755, true);
            $filename = uniqid('article_') . '.' . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $path . $filename)) {
                if ($currentImage && file_exists($currentImage)) {
                    unlink($currentImage);
                }
                $currentImage = $path . $filename;
            } else {
                $messages['errors'][] = "Erreur lors du téléchargement de la nouvelle image.";
            }
        }
    }

  
    // Validation des données
    if (empty($title) || empty($slug) || empty($introduction) || empty($content)) {
        $messages['errors'][] = 'Veuillez remplir tous les champs obligatoires du formulaire !';
    } else {
        // Mise à jour de l'article dans la base de données
        if (updateArticle((int) $articleId, $title, $slug, $introduction, $content, $currentImage)) {
            $messages['success'][] = 'Article mis à jour avec succès!';
            // Rafraîchir les données
            $article = findArticle((int) $articleId);
            $currentImage = $article['image'] ?? null;
        } else {
            $messages['errors'][] = 'Aucune modification détectée ou erreur lors de la mise à jour';
        }
    }

    // -- Redirection vers la page d'admin
    redirect('admin-list-article.php');
}

$pageTitle = 'Éditer un article';

render('admin/articles/admin-update-article', [
    'pageTitle' => $pageTitle,
    'article' => $article,
    'articleId' => $articleId,
    'messages' => $messages
], 'admin-layout');
