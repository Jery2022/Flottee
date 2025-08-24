<?php

namespace App\Models;

use PDO;

class UsersModel
{
    protected PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = getPDO();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Récupère les utilisateurs avec filtres et pagination
    public function getPaginated(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE deleted_at IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  Récupère un utilisateur par ID s’il n’est pas supprimé
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    //  Récupère un utilisateur par ID, y compris s’il est supprimé
    public function getByIdIncludingDeleted(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    //  Récupère un utilisateur par email s’il n’est pas supprimé
    public function getByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    //  Vérifie si un email existe parmi les utilisateurs non supprimés
    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    //  Crée un nouvel utilisateur
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (first_name, last_name, pseudo, email, password, role)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['pseudo'],
            $data['email'],
            $data['password'],
            $data['role']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    //  Met à jour un utilisateur existant
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    //  Suppression logique : marque l’utilisateur comme supprimé
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function restore($id)
    {
        $sql = "UPDATE users SET deleted_at = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPseudo($pseudo)
    {
        $sql = "SELECT * FROM users WHERE pseudo = :pseudo LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':pseudo' => $pseudo
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
