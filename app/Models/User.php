<?php

declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class  User extends Model
{
    public function __construct(
        public readonly int $id = 0,
        public string       $username = '',
         public string $email = '',
        public string       $password = '',
        public string       $role = 'user',
        public string       $created_at = '',
    )
    {
        parent::__construct(); // On appelle Model pour récupérer $this->pdo
    }

    public static function count(): int
    {
        $instance = new self();
        return (int)$instance->pdo->prepare('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public static function find(int $id): array
    {
        $instance = new self();
        $sql = 'SELECT * FROM users WHERE id = :id';
        $query = $instance->pdo->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les utilisateurs (tous rôles confondus)
     */
    public static function findAll(): array
    {
        $instance = new self();
        $query = $instance->pdo->query('SELECT * FROM users ORDER BY created_at DESC');
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    function findUserById(int $id): array
    {
         $instance = new self();
        $query = 'SELECT * FROM users WHERE id = :id';
        $req = $instance->pdo->prepare($query);
        $req->execute(compact('id'));
        return  $req->fetch();
    }
    public static function findUserBynameExcept(string $username,int $id): array|false
    {
         $instance = new self();
        $query = 'SELECT * FROM users WHERE username = :username AND id != :id';
        $req = $instance->pdo->prepare($query);
        $req->execute(compact('username', 'id'));
        return  $req->fetch();
    }
    /**
     * Récupère un utilisateur par son pseudo (strict)
     */
    public static function findByUsername(string $username): array|false
    {
         $instance = new self();
        $sql = 'SELECT * FROM users WHERE username = :username';
        $query = $instance->pdo->prepare($sql);
        $query->execute(['username' => $username]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    public static function findByEmailExcept(string $email,int $id): array|false
    {
         $instance = new self();
        $query = 'SELECT * FROM users WHERE email = :email AND id != :id';
        $req = $instance->pdo->prepare($query);
        $req->execute(compact('email', 'id'));
        return  $req->fetch();
    }

    /**
     * Récupère un utilisateur soit par son email, soit par son pseudo (pour le login)
     */
    public static function findByEmailOrUsername(string $identifier): ?self
    {
         $instance = new self();
        $sql = 'SELECT * FROM users WHERE email = :identifier OR username = :identifier';
        $query = $instance->pdo->prepare($sql);
        $query->execute(['identifier' => $identifier]);
        //  return $query->fetch(PDO::FETCH_ASSOC);
          $row = $query->fetch(PDO::FETCH_ASSOC);
          return $row ? self::fromRow($row) : null;
    }


    /**
     * Ajoute un nouvel utilisateur (Inscription)
     */
    public static  function save(string $username, string $email, string $password, string $role = 'USER'): bool
    {
         $instance = new self();
        $sql = 'INSERT INTO users (username, email, password, role, created_at) 
            VALUES (:username, :email, :password, :role, NOW())';

        $query = $instance->pdo->prepare($sql);
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
    public static  function update(int $id, string $username, string $email, ?string $password = null): bool
    {
         $instance = new self();

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

        $query = $instance->pdo->prepare($sql);
        $query->execute($params);

        // Retourne true même si rien n'a été modifié, l'important c'est que la requête n'ait pas échoué
        return true;
    }

    /**
     * Récupère un utilisateur par son adresse email (strict)
     */
    public static  function findUserByEmail(string $email): array|false
    {
         $instance = new self();
        $sql = 'SELECT * FROM users WHERE email = :email';
        $query = $instance->pdo->prepare($sql);
        $query->execute(['email' => $email]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les utilisateurs avec leur nombre de commentaires
     */
    public static function findUsersWithCommentCount(): array
    {
         $instance = new self();
        $query = $instance->pdo->query('
        SELECT u.id, u.username, COUNT(c.id) AS comment_count
        FROM users u
        LEFT JOIN comments c ON u.id = c.user_id
        GROUP BY u.id
    ');
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

  /**
     * Retourne les utilisateurs avec leur nombre de commentaires (pour l'admin).
     */
    public static function allWithCommentCount(): array
    {
        $instance = new self();
        return $instance->pdo->query('
            SELECT u.id, u.username, COUNT(c.id) AS comment_count
            FROM users u
            LEFT JOIN comments c ON u.id = c.user_id
            GROUP BY u.id
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

      /**
     * Écrit les données de l'utilisateur dans la session.
     */
    public function toSession(): void
    {
        $_SESSION['auth'] = [
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,
            'role'     => $this->role,
        ];
    }

     /**
     * Fabrique un objet User depuis un tableau associatif (ligne BDD).
     */
    private static function fromRow(array $row): self
    {
        return new self(
            id:         (int) $row['id'],
            username:   $row['username'],
            email:      $row['email'],
            password:   $row['password'],
            role:       $row['role'],
            created_at: $row['created_at'] ?? '',
        );
    }


    // =========================================================================
    // MÉTHODES D'INSTANCE — Actions sur l'objet courant
    // =========================================================================

    /**
     * Vérifie si le mot de passe fourni correspond au hash stocké.
     */
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    /**
     * Hache et stocke un nouveau mot de passe.
     */
    public function hashPassword(string $plainPassword): void
    {
        $this->password = password_hash($plainPassword, PASSWORD_DEFAULT);
    }
}