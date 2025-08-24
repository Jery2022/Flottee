<?php

namespace App\Models;

use PDO;

class ReservationModel
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

        $sql = "SELECT r.*, 
                       v.make, v.model, v.license_plate,
                       u.first_name, u.last_name
                FROM reservations r
                JOIN vehicles v ON r.vehicle_id = v.id
                JOIN users u ON r.user_id = u.id";

        $countSql = "SELECT COUNT(*) 
                     FROM reservations r
                     JOIN vehicles v ON r.vehicle_id = v.id
                     JOIN users u ON r.user_id = u.id";

        $where = [];
        $params = [];

        if (!empty($filters['vehicle'])) {
            $where[] = "(LOWER(v.make) LIKE :vehicle OR LOWER(v.model) LIKE :vehicle OR LOWER(v.license_plate) LIKE :vehicle)";
            $params[':vehicle'] = '%' . strtolower($filters['vehicle']) . '%';
        }
        if (!empty($filters['user'])) {
            $where[] = "(LOWER(u.first_name) LIKE :user OR LOWER(u.last_name) LIKE :user)";
            $params[':user'] = '%' . strtolower($filters['user']) . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = "r.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
            $countSql .= " WHERE " . implode(' AND ', $where);
        }

        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql .= " ORDER BY r.start_date DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add formatted info strings for easier display on the frontend
        foreach ($reservations as &$reservation) {
            $reservation['vehicle_info'] = "{$reservation['make']} {$reservation['model']} ({$reservation['license_plate']})";
            $reservation['user_info'] = "{$reservation['first_name']} {$reservation['last_name']}";
        }

        return [
            'reservations' => $reservations,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage)
        ];
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT r.*, 
                       v.make, v.model, v.license_plate,
                       u.first_name, u.last_name
                FROM reservations r
                JOIN vehicles v ON r.vehicle_id = v.id
                JOIN users u ON r.user_id = u.id
                WHERE r.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            $reservation['vehicle_info'] = "{$reservation['make']} {$reservation['model']} ({$reservation['license_plate']})";
            $reservation['user_info'] = "{$reservation['first_name']} {$reservation['last_name']}";
        }

        return $reservation ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO reservations (vehicle_id, user_id, start_date, end_date, status) 
                VALUES (:vehicle_id, :user_id, :start_date, :end_date, :status)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':vehicle_id' => $data['vehicle_id'],
            ':user_id' => $data['user_id'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':status' => $data['status']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE reservations 
                SET vehicle_id = :vehicle_id, 
                    user_id = :user_id, 
                    start_date = :start_date, 
                    end_date = :end_date, 
                    status = :status 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':vehicle_id' => $data['vehicle_id'],
            ':user_id' => $data['user_id'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM reservations WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
