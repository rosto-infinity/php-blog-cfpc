<?php

declare(strict_types=1);
require_once __DIR__ . '/Enums/Role.php';
require_once __DIR__ . '/functions/article.php';
require_once __DIR__ . '/functions/user.php';
require_once __DIR__ . '/functions/comment.php';

/**
 * Vérifie si l'utilisateur est connecté
 */
function checkAuth(): void
{
    if (!isset($_SESSION['auth']) || !is_array($_SESSION['auth'])) {
        redirect('login.php');
    }
}

/**
 * Vérifie si l'utilisateur est administrateur
 */
function checkAdmin(): void
{
    checkAuth();
    if ($_SESSION['auth']['role'] !== Role::ADMIN->value) {
        redirect('index.php');
    }
}

if (!function_exists('clean_input')) {
    function clean_input(string $data): string
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}

if (!function_exists('createSlug')) {
    function createSlug(string $title): string
    {
        $title = removeAccents($title);
        $slug = strtolower(str_replace(' ', '-', $title));
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);

        return trim($slug, '-');
    }
}

if (!function_exists('removeAccents')) {
    function removeAccents(string $string): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ý' => 'Y',
        ];

        return strtr($string, $accents);
    }
}

function render(string $path, array $variables = [], string $layout = 'blog-layout')
{
  // [
  //   'var1' => 1,
  //   'var2' => 2,
  //   'var3' => 3,
  // ]

  // $var1 = 1,
  // $var2 =2,
  // $var3 = 3,
extract($variables);
ob_start();
require_once "resources/views/" .$path."_html.php";
$pageContent = ob_get_clean();
require_once "resources/views/layouts/{$layout}/{$layout}_html.php";
}

function redirect(string $path){
  header("Location: $path");
  exit(); // Terminer le script
}
