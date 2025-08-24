<?php

namespace App\Models;

require_once __DIR__ . '/../../config/db.php';

class StatsModel
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = \getPDO();
    }

    /**
     * 1. Taux d’utilisation des véhicules.
     * Pourcentage de véhicules actuellement 'en utilisation'.
     */
    public function getVehicleUtilizationRate(): float
    {
        $stmt = $this->pdo->query("SELECT IF(COUNT(*) > 0, (COUNT(CASE WHEN status = 'en utilisation' THEN 1 END) * 100.0 / COUNT(*)), 0) as rate FROM vehicles");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return round((float)($result['rate'] ?? 0), 2);
    }

    /**
     * 2. Nombre d’utilisateurs actifs.
     */
    public function getActiveUserCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    /**
     * 3. Kilométrage moyen par véhicule.
     * Note: Le kilométrage n'est pas dans le schéma actuel.
     */
    public function getAverageMileagePerVehicle(): float
    {
        // Ne peut pas être implémenté avec le schéma actuel.
        return 0.0;
    }

    /**
     * 4. Fréquence moyenne de maintenance par véhicule.
     */
    public function getAverageMaintenanceFrequency(): float
    {
        $stmt = $this->pdo->query("
            SELECT IF(v.total > 0, m.total / v.total, 0) as avg_freq
            FROM 
                (SELECT COUNT(*) as total FROM maintenance_records) as m,
                (SELECT COUNT(*) as total FROM vehicles) as v
        ");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return round((float)($result['avg_freq'] ?? 0), 2);
    }

    /**
     * 5. Coût total de maintenance par véhicule.
     */
    public function getTotalMaintenanceCostPerVehicle(): array
    {
        $stmt = $this->pdo->query("
            SELECT v.make, v.model, v.license_plate, SUM(mr.cost) as total_cost
            FROM maintenance_records mr
            JOIN vehicles v ON mr.vehicle_id = v.id
            GROUP BY mr.vehicle_id
            ORDER BY total_cost DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 6. Durée moyenne d’affectation.
     */
    public function getAverageAssignmentDuration(): float
    {
        $stmt = $this->pdo->query("
            SELECT AVG(DATEDIFF(end_date, start_date)) as avg_duration
            FROM assignments
            WHERE end_date IS NOT NULL AND status = 'completed'
        ");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return round((float)($result['avg_duration'] ?? 0), 2);
    }

    /**
     * 7. Nombre de véhicules en maintenance actuellement.
     */
    public function getVehiclesInMaintenanceCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM vehicles WHERE status = 'en maintenance'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    /**
     * 8. Alertes ou maintenances à venir dans 60 jours.
     * Note: Le schéma actuel ne contient pas de date de maintenance future.
     */
    public function getUpcomingMaintenanceAlerts(): array
    {
        // Ne peut pas être implémenté avec le schéma actuel.
        return [];
    }

    /**
     * Top 5 des véhicules les plus utilisés (basé sur le nombre d'affectations).
     */
    public function getTopUsedVehicles(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                v.make, v.model, v.license_plate, 
                SUM(DATEDIFF(IFNULL(a.end_date, CURDATE()), a.start_date)) as total_usage_days
            FROM assignments a
            JOIN vehicles v ON a.vehicle_id = v.id
            WHERE a.status IN ('active', 'completed')
            GROUP BY a.vehicle_id
            ORDER BY total_usage_days DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Top 5 des utilisateurs (basé sur le nombre d'affectations).
     */
    public function getTopUsers(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                u.first_name, u.last_name, 
                SUM(DATEDIFF(IFNULL(a.end_date, CURDATE()), a.start_date)) as total_usage_days
            FROM assignments a
            JOIN users u ON a.user_id = u.id
            WHERE a.status IN ('active', 'completed')
            GROUP BY a.user_id
            ORDER BY total_usage_days DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
