<?php
namespace App\Models;

class StatsModel
{
    public function getFleetStats(): array
    {
        return [
            'disponibilite' => 80,
            'utilisation_mensuelle' => [
                'Janvier' => 60,
                'Février' => 75,
                'Mars' => 50,
                'Avril' => 90
            ],
            'marques_utilisees' => [
                'Toyota' => 3,
                'Renault' => 1,
                'Peugeot' => 1
            ],
            'marques_en_panne' => [
                'Renault' => 2,
                'Peugeot' => 1
            ]
        ];
    }
}