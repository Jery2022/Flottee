<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrateur - Flottee</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome pour les icônes -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .sidebar {
      background-color: #343a40;
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      padding-top: 20px;
    }

    .sidebar a {
      color: #adb5bd;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
    }

    .sidebar a:hover,
    .sidebar a.active {
      color: white;
      background-color: #495057;
    }

    .main-content {
      margin-left: 250px;
      padding: 20px;
    }

    .stat-card {
      border: none;
      border-radius: 0.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      color: white;
    }

    .stat-card .card-body {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .stat-card i {
      font-size: 3rem;
      opacity: 0.5;
    }

    .bg-c-blue {
      background: linear-gradient(45deg, #4099ff, #73b4ff);
    }

    .bg-c-green {
      background: linear-gradient(45deg, #2ed8b6, #59e0c5);
    }

    .bg-c-yellow {
      background: linear-gradient(45deg, #FFB64D, #ffcb80);
    }

    .bg-c-pink {
      background: linear-gradient(45deg, #FF5370, #ff869a);
    }

    .bg-c-purple {
      background: linear-gradient(45deg, #6A1B9A, #9C27B0);
    }

    .bg-c-teal {
      background: linear-gradient(45deg, #009688, #4DB6AC);
    }

    .bg-c-orange {
      background: linear-gradient(45deg, #FF9800, #FFB74D);
    }

    .bg-c-red {
      background: linear-gradient(45deg, #F44336, #E57373);
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <h4 class="text-center">Flottee Admin</h4>
    <hr class="bg-light">
    <a href="#" class="active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
    <a href="#"><i class="fas fa-car me-2"></i>Véhicules</a>
    <a href="#"><i class="fas fa-users me-2"></i>Utilisateurs</a>
    <a href="#"><i class="fas fa-tools me-2"></i>Maintenance</a>
    <a href="#" id="logout-btn" class="mt-auto"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a>
  </div>

  <main class="main-content">
    <div class="container-fluid">
      <h1 class="mb-4">Dashboard</h1>
      <div id="stats-container" class="row">
        <!-- Les cartes de statistiques seront injectées ici par JavaScript -->
        <div class="col-12 text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              Coût total de maintenance par véhicule
            </div>
            <div class="card-body">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Véhicule</th>
                    <th>Plaque</th>
                    <th>Coût Total</th>
                  </tr>
                </thead>
                <tbody id="maintenance-cost-table">
                  <!-- Données injectées par JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const token = localStorage.getItem('jwt');

      if (!token) {
        window.location.href = '/login.php';
        return;
      }

      fetch('/admin/dashboard', {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        .then(response => {
          if (response.status === 401 || response.status === 403) {
            localStorage.removeItem('jwt');
            window.location.href = '/login.php';
            return;
          }
          if (!response.ok) {
            throw new Error('Erreur réseau ou serveur');
          }
          return response.json();
        })
        .then(data => {
          if (data && data.stats) {
            displayStats(data.stats);
          }
        })
        .catch(error => {
          console.error('Erreur lors de la récupération des statistiques:', error);
          const container = document.getElementById('stats-container');
          container.innerHTML = `<div class="alert alert-danger">Erreur lors du chargement des données.</div>`;
        });

      function displayStats(stats) {
        const container = document.getElementById('stats-container');
        container.innerHTML = ''; // Vider le spinner

        const statsMap = [{
            title: 'Utilisateurs Actifs',
            value: stats.active_user_count,
            icon: 'fa-users',
            color: 'bg-c-blue'
          },
          {
            title: 'Taux d\'Utilisation',
            value: stats.vehicle_utilization_rate + '%',
            icon: 'fa-chart-pie',
            color: 'bg-c-green'
          },
          {
            title: 'Véhicules en Maintenance',
            value: stats.vehicles_in_maintenance_count,
            icon: 'fa-tools',
            color: 'bg-c-yellow'
          },
          {
            title: 'Durée Moy. Affectation',
            value: (stats.average_assignment_duration || 0) + ' jours',
            icon: 'fa-calendar-alt',
            color: 'bg-c-pink'
          }
        ];

        statsMap.forEach(stat => {
          const cardHtml = `
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card stat-card ${stat.color}">
                                <div class="card-body">
                                    <div>
                                        <h4 class="mb-0">${stat.value}</h4>
                                        <span>${stat.title}</span>
                                    </div>
                                    <i class="fas ${stat.icon}"></i>
                                </div>
                            </div>
                        </div>
                    `;
          container.innerHTML += cardHtml;
        });

        // Remplir le tableau des coûts de maintenance
        const tableBody = document.getElementById('maintenance-cost-table');
        let tableHtml = '';
        if (stats.total_maintenance_cost_per_vehicle.length > 0) {
          stats.total_maintenance_cost_per_vehicle.forEach(item => {
            tableHtml += `
                            <tr>
                                <td>${item.make} ${item.model}</td>
                                <td>${item.license_plate}</td>
                                <td>${parseFloat(item.total_cost).toFixed(2)} €</td>
                            </tr>
                        `;
          });
        } else {
          tableHtml = '<tr><td colspan="3" class="text-center">Aucune donnée de maintenance disponible.</td></tr>';
        }
        tableBody.innerHTML = tableHtml;
      }

      // Gestion de la déconnexion
      document.getElementById('logout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        localStorage.removeItem('jwt');
        window.location.href = '/login.php';
      });
    });
  </script>
</body>

</html>