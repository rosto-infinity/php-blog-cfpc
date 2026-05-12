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
if (isset($_POST['add-article'])) {
  $title = clean_input((string)($_POST['title'] ?? ''));
  $slug = createSlug($title);
  $introduction = clean_input((string)($_POST['introduction'] ?? ''));
  $content = $_POST['content'];
  $imagePath = null;

  //Étape 1 : Détection du fichier
  if (!empty($_FILES['image']['name'])  && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $path = 'storage/articles/';

    //Étape 2 : Sécurisation (Format et Taille)
    $error = match (true) {
      !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => "Format d'image non surporté.",
      $file['size'] > 2097152 => "Fichier trop lourd (limite : 2 Mo)",
      default => null
    };
    //Étape 3 : Création du dossier et Nommage unique
    if (!$error) {
      if (!is_dir($path)) mkdir($path, 0755, true);
      $filename = uniqid('article_') . '.' . $ext;
  //Étape 4 : Le transfert définitif
      if (move_uploaded_file($file['tmp_name'], $path . $filename)) {
        $imagePath = $path . $filename;
        
      } else {
        $error = "Erreur de téléchargement.";
      }
    }
  }

  // Validation des données
  if (empty($title) || empty($slug) || empty($introduction) || empty($content)) {
    $error = '---Veuillez remplir tous les champs obligatoires du formulaire--- !';
  } else {
    $query = $pdo->prepare('SELECT * FROM articles WHERE slug = :slug');
    $query->execute([':slug' => $slug]);
    if ($query->fetchColumn() > 0) {
      $error = " Le slug '$slug' existe déjà. Vueillez en choisir un autre";
    } else {
      $query = $pdo->prepare('INSERT INTO articles
          (title, slug, introduction, content, image, created_at)
          VALUES (:title, :slug, :introduction, :content,:image,NOW())');
      $query->execute([
        'title' => $title,
        'slug' => $slug,
        'introduction' => $introduction,
        'content' => $content,
        'image' => $imagePath,
      ]);
      if ($query->rowCount() > 0) {
        flash_set('success', 'Articles créé avec succès');
        header('Location: admin-list-article.php');
        exit();
      } else {
        $error = "Erreur lors de la création de l'article";
      }
    }
  }
}



$pageTitle = 'Add Articles';
ob_start();
require_once 'resources/views/admin/articles/admin-add-article_html.php';
$pageContent = ob_get_clean();
require_once 'resources/views/layouts/admin-layout/admin-layout_html.php';
