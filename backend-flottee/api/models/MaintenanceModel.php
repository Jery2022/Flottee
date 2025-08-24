<?php
// backend-flottee/api/models/MaintenanceModel.php

namespace App\Models;

use PDO;

class MaintenanceModel
{
    private $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = getPDO();
    }

    public function getPaginated(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        // Base queries
        $sql = "SELECT mr.*, v.make, v.model, v.license_plate 
                FROM maintenance_records mr
                JOIN vehicles v ON mr.vehicle_id = v.id";
        $countSql = "SELECT COUNT(mr.id) 
                     FROM maintenance_records mr
                     JOIN vehicles v ON mr.vehicle_id = v.id";

        $where = [];
        $params = [];

        if (!empty($filters['vehicle_id'])) {
            $where[] = "mr.vehicle_id = :vehicle_id";
            $params[':vehicle_id'] = $filters['vehicle_id'];
        }

        if (!empty($filters['type'])) {
            $where[] = "mr.type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['status'])) {
            $where[] = "mr.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['severity'])) {
            $where[] = "mr.severity = :severity";
            $params[':severity'] = $filters['severity'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
            $countSql .= " WHERE " . implode(' AND ', $where);
        }

        // Get total count
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Get paginated data
        $sql .= " ORDER BY mr.date DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $maintenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'maintenances' => $maintenances,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage)
        ];
    }

    public function getById($id)
    {
        $query = "SELECT * FROM maintenance_records WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO maintenance_records (vehicle_id, type, status, severity, description, date, cost) 
                  VALUES (:vehicle_id, :type, :status, :severity, :description, :date, :cost)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':vehicle_id', $data['vehicle_id']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':severity', $data['severity']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':cost', $data['cost']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $query = "UPDATE maintenance_records 
                  SET vehicle_id = :vehicle_id, type = :type, status = :status, severity = :severity, description = :description, date = :date, cost = :cost 
                  WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':vehicle_id', $data['vehicle_id']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':severity', $data['severity']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':cost', $data['cost']);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM maintenance_records WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
