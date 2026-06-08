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

$article = null;
$currentImage = null;

// Récupération des informations d'un article à modifier
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $articleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $article   = Article::find((int) $articleId);

    // Récupération des données APRÈS la requête
    $title        = $article->title ?? '';
    $slug         = $article->slug ?? '';
    $introduction = $article->introduction ?? '';
    $content      = $article->content ?? '';
    $currentImage = $article->image ?? null;
}

// Traitement de la soumission du formulaire
if (isset($_POST['update'])) {
    $articleId    = clean_input((string) $_POST['id']);
    $title        = clean_input((string) filter_input(INPUT_POST, 'title', FILTER_DEFAULT));
    $slug         = strtolower(str_replace(' ', '-', $title));
    $introduction = clean_input((string) filter_input(INPUT_POST, 'introduction', FILTER_DEFAULT));
    $content      = $_POST['content'] ?? '';

    // Traitement de l'image uploadée
    if (!empty($_FILES['a_image']['name']) && $_FILES['a_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['a_image'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $path = 'storage/articles/';

        $errorMsg = match (true) {
            !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => "Format d'image non supporté.",
            $file['size'] > 2097152                                 => "Fichier trop lourd (limite : 2 Mo).",
            default                                                 => null
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
        // Mise à jour via le modèle Article
        $article               = Article::find((int) $articleId);
        $article->title        = $title;
        $article->slug         = $slug;
        $article->introduction = $introduction;
        $article->content      = $content;
        $article->image        = $currentImage;

        if ($article->save()) {
            $messages['success'][] = 'Article mis à jour avec succès!';
            $article = Article::find((int) $articleId);
        } else {
            $messages['errors'][] = 'Aucune modification détectée ou erreur lors de la mise à jour';
        }
    }

    redirect('admin-list-article.php');
}
$pageTitle = 'Éditer un article';

render('admin/articles/admin-update-article', [
    'pageTitle' => $pageTitle,
    'article' => $article,
    'articleId' => $articleId,
    'messages' => $messages
], 'admin-layout');
