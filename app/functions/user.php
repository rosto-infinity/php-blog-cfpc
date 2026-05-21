<?php

declare(strict_types=1);

require_once __DIR__ . '/../../database/database.php';

/**
 * Retourne le nombre total d'utilisateurs
 */
function countUsers(): int
{
    $pdo = getPdo();
    $query = $pdo->query('SELECT COUNT(*) FROM users');
    return (int) $query->fetchColumn();
}

function findUserById(int $id): array
{
    $pdo = getPdo();
    $query = 'SELECT * FROM users WHERE id = :id';
    $req = $pdo->prepare($query);
    $req->execute(compact('id'));
    return  $req->fetch();
}
function findUserByUsernameExcept(string $username,int $id): array|false
{
    $pdo = getPdo();
    $query = 'SELECT * FROM users WHERE username = :username AND id != :id';
        $req = $pdo->prepare($query);
        $req->execute(compact('username', 'id'));
    return  $req->fetch();
}
function findUserByEmailExcept(string $email,int $id): array|false
{
    $pdo = getPdo();
    $query = 'SELECT * FROM users WHERE email = :email AND id != :id';
        $req = $pdo->prepare($query);
        $req->execute(compact('email', 'id'));
    return  $req->fetch();
}