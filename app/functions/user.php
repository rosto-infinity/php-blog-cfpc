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
/**
 * Récupère tous les utilisateurs (tous rôles confondus)
 */
function findAllUsers(): array
{
    $pdo = getPdo();
    $query = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
    return $query->fetchAll(PDO::FETCH_ASSOC);
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
/**
 * Récupère un utilisateur par son pseudo (strict)
 */
function findUserByUsername(string $username): array|false
{
    $pdo = getPdo();
    $sql = 'SELECT * FROM users WHERE username = :username';
    $query = $pdo->prepare($sql);
    $query->execute(['username' => $username]);
    return $query->fetch(PDO::FETCH_ASSOC);
}


function findUserByEmailExcept(string $email,int $id): array|false
{
    $pdo = getPdo();
    $query = 'SELECT * FROM users WHERE email = :email AND id != :id';
        $req = $pdo->prepare($query);
        $req->execute(compact('email', 'id'));
    return  $req->fetch();
}

/**
 * Récupère un utilisateur soit par son email, soit par son pseudo (pour le login)
 */
function findUserByEmailOrUsername(string $identifier): array|false
{
    $pdo = getPdo();
    $sql = 'SELECT * FROM users WHERE email = :identifier OR username = :identifier';
    $query = $pdo->prepare($sql);
    $query->execute(['identifier' => $identifier]);
    return $query->fetch(PDO::FETCH_ASSOC);
}


/**
 * Ajoute un nouvel utilisateur (Inscription)
 */
function insertUser(string $username, string $email, string $password, string $role = 'USER'): bool
{
    $pdo = getPdo();
    $sql = 'INSERT INTO users (username, email, password, role, created_at) 
            VALUES (:username, :email, :password, :role, NOW())';
            
    $query = $pdo->prepare($sql);
    return $query->execute([
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'role' => $role,
    ]);
}


/**
 * Met à jour le profil d'un utilisateur
 */
function updateUser(int $id, string $username, string $email, ?string $password = null): bool
{
    $pdo = getPdo();
    
    // Construction dynamique de la requête selon si un nouveau mot de passe est fourni ou non
    $sql = 'UPDATE users SET username = :username, email = :email';
    $params = [
        'username' => $username,
        'email' => $email,
        'id' => $id,
    ];
    
    if ($password !== null && $password !== '') {
        $sql .= ', password = :password';
        $params['password'] = $password;
    }
    
    $sql .= ' WHERE id = :id';
    
    $query = $pdo->prepare($sql);
    $query->execute($params);
    
    // Retourne true même si rien n'a été modifié, l'important c'est que la requête n'ait pas échoué
    return true; 
}

/**
 * Récupère un utilisateur par son adresse email (strict)
 */
function findUserByEmail(string $email): array|false
{
    $pdo = getPdo();
    $sql = 'SELECT * FROM users WHERE email = :email';
    $query = $pdo->prepare($sql);
    $query->execute(['email' => $email]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

/**
 * Récupère les utilisateurs avec leur nombre de commentaires
 */
function findUsersWithCommentCount(): array
{
    $pdo = getPdo();
    $query = $pdo->query('
        SELECT u.id, u.username, COUNT(c.id) AS comment_count
        FROM users u
        LEFT JOIN comments c ON u.id = c.user_id
        GROUP BY u.id
    ');
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
