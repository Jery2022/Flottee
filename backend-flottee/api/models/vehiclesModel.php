<?php

namespace App\Models;

use PDO;

class vehiclesModel
{
    protected PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = getPDO();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getPaginated(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM vehicles";
        $countSql = "SELECT COUNT(*) FROM vehicles";
        $where = [];
        $params = [];

        if (!empty($filters['name'])) {
            $where[] = "(LOWER(make) LIKE LOWER(:name) OR LOWER(model) LIKE LOWER(:name))";
            $params[':name'] = '%' . $filters['name'] . '%';
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

        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
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
