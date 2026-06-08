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
// Récupération des données des entrées de l'utilisateur
if (isset($_POST['add-article'])) {
    $title = clean_input((string) ($_POST['title'] ?? ''));
    $slug = createSlug($title);
    $introduction = clean_input((string) ($_POST['introduction'] ?? ''));
    $content = $_POST['content'];
    $imagePath = null;

    // Traitement de l'image
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $path = 'storage/articles/';

        $error = match(true) {
            !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => "Format d'image non supporté.",
            $file['size'] > 2097152 => "Fichier trop lourd (limite : 2 Mo).",
            default => null
        };

        if (!$error) {
            if (!is_dir($path)) mkdir($path, 0755, true);
            $filename = uniqid('article_') . '.' . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $path . $filename)) {
                $imagePath = $path . $filename;
            } else {
                $error = "Erreur de téléchargement.";
            }
        }
    }

    // Validation des données
    if (empty($title) || empty($slug) || empty($introduction) || empty($content)) {
        $error = 'Veuillez remplir tous les champs obligatoires du formulaire !';
    } else {
        // Vérification de l'unicité du slug
        if (Article::slugExists($slug)) {
            $error = "Le slug '$slug' existe déjà. Veuillez en choisir un autre.";
        } else {
            // Création de l'objet Article et sauvegarde
            $article = new Article(title: $title, slug: $slug, introduction: $introduction, content: $content, image: $imagePath);
            if ($article->save()) {
                $_SESSION['success']['update'] = 'Article créé avec succès!';
                redirect('admin-list-article.php');
            } else {
                $error = "Erreur lors de la création de l'article";
            }
        }
    }
}

// Récupération de tous les articles
$allArticles = Article::findAll();

$pageTitle = 'Page  Add articles';

// Début du tampon de la page de sortie
render('admin/articles/admin-add-article', [
    'pageTitle' => $pageTitle,
    'error' => $error ?? null,
    'allArticles' => $allArticles
], 'admin-layout');