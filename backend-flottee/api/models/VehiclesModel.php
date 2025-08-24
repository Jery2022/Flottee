<?php

namespace App\Models;

use PDO;

class VehiclesModel
{
    protected PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = getPDO();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY make, model";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaginated(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM vehicles";
        $countSql = "SELECT COUNT(*) FROM vehicles";
        $where = [];
        $params = [];

        if (!empty($filters['name'])) {
            $where[] = "(LOWER(make) LIKE :make_name OR LOWER(model) LIKE :model_name)";
            $params[':make_name'] = '%' . strtolower($filters['name']) . '%';
            $params[':model_name'] = '%' . strtolower($filters['name']) . '%';
        }
        if (!empty($filters['type'])) {
            $where[] = "type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }
        if (isset($filters['visibility'])) {
            if ($filters['visibility'] === 'deleted') {
                $where[] = "deleted_at IS NOT NULL";
            } else {
                $where[] = "deleted_at IS NULL";
            }
        } else {
            $where[] = "deleted_at IS NULL";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
            $countSql .= " WHERE " . implode(' AND ', $where);
        }

        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Pour éviter les problèmes de binding avec LIMIT et OFFSET, on les injecte en tant qu'entiers.
        $sql .= " ORDER BY id DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        $stmt = $this->pdo->prepare($sql);

        // On exécute la requête avec les paramètres de filtres
        $stmt->execute($params);
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'vehicles' => $vehicles,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage)
        ];
    }
}
