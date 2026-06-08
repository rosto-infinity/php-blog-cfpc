<?php
declare(strict_types=1);

require_once __DIR__ . '/../../database/database.php';

/**
 * Classe de base pour tous les modèles.
 * Elle centralise la connexion PDO via getPdo() (Singleton).
 * Toute classe qui extends Model hérite de $this->pdo.
 */
class Model
{
    // protected = accessible dans Model, ET dans Article, User, etc.
    // PDO = typage strict, on garantit que c'est un objet PDO.
    protected PDO $pdo;

    public function __construct()
    {
        // On récupère la connexion une seule fois à la création de l'objet
        $this->pdo = getPdo();
    }
}