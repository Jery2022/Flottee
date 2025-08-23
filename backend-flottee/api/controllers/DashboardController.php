<?php

namespace App\Controllers;

use Core\Response;
use App\Helpers\JWTHelper;
use App\Models\StatsModel;

class DashboardController
{
    public function index()
    {
        // Le middleware s'occupe de la validation du token et du rôle.
        \App\Helpers\AuthMiddleware::requireRole('admin');

        // Si nous arrivons ici, l'utilisateur est authentifié et est un admin.
        $user = \App\Helpers\JWTHelper::getUserDataFromJWT();

        try {
            $statsModel = new StatsModel();

            $stats = [
                'vehicle_utilization_rate' => $statsModel->getVehicleUtilizationRate(),
                'active_user_count' => $statsModel->getActiveUserCount(),
                'average_mileage_per_vehicle' => $statsModel->getAverageMileagePerVehicle(),
                'average_maintenance_frequency' => $statsModel->getAverageMaintenanceFrequency(),
                'total_maintenance_cost_per_vehicle' => $statsModel->getTotalMaintenanceCostPerVehicle(),
                'average_assignment_duration' => $statsModel->getAverageAssignmentDuration(),
                'vehicles_in_maintenance_count' => $statsModel->getVehiclesInMaintenanceCount(),
                'upcoming_maintenance_alerts' => $statsModel->getUpcomingMaintenanceAlerts(),
            ];

            // Renvoyer une réponse de succès avec les statistiques
            Response::json([
                'status' => 'success',
                'message' => 'Statistiques du dashboard récupérées avec succès.',
                'user' => $user,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            // Gérer les erreurs potentielles lors de la récupération des statistiques
            Response::error("Erreur lors de la récupération des statistiques: " . $e->getMessage(), 500);
        }
    }
}
