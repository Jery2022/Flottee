<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrateur - Flottee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <div class="col-12 text-center">
          <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-lg-12 mb-4">
          <div class="card">
            <div class="card-header"><i class="fas fa-dollar-sign me-2"></i>Top 5 - Coûts de Maintenance</div>
            <div class="card-body">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Véhicule</th>
                    <th>Coût Total</th>
                  </tr>
                </thead>
                <tbody id="maintenance-cost-table"></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-12 mb-4">
          <div class="card">
            <div class="card-header"><i class="fas fa-chart-bar me-2"></i>Top 5 Utilisation (en jours)</div>
            <div class="card-body"><canvas id="usageChart" style="max-height: 400px;"></canvas></div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            return Promise.reject('Session invalide');
          }
          return response.json();
        })
        .then(data => {
          if (data && data.stats) {
            displayStats(data.stats);
            createUsageChart(data.stats.top_used_vehicles, data.stats.top_users);
          }
        })
        .catch(error => {
          console.error('Erreur:', error);
          document.getElementById('stats-container').innerHTML = `<div class="alert alert-danger">Erreur lors du chargement des données.</div>`;
        });

      function displayStats(stats) {
        const container = document.getElementById('stats-container');
        container.innerHTML = '';
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
            title: 'En Maintenance',
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
          container.innerHTML += `<div class="col-md-6 col-xl-3 mb-4"><div class="card stat-card ${stat.color}"><div class="card-body"><div><h4 class="mb-0">${stat.value}</h4><span>${stat.title}</span></div><i class="fas ${stat.icon}"></i></div></div></div>`;
        });

        const tableBody = document.getElementById('maintenance-cost-table');
        let tableHtml = '';
        if (stats.total_maintenance_cost_per_vehicle.length > 0) {
          stats.total_maintenance_cost_per_vehicle.forEach(item => {
            tableHtml += `<tr><td>${item.make} ${item.model} (${item.license_plate})</td><td>${parseFloat(item.total_cost).toFixed(2)} €</td></tr>`;
          });
        } else {
          tableHtml = '<tr><td colspan="2" class="text-center">Aucune donnée.</td></tr>';
        }
        tableBody.innerHTML = tableHtml;
      }

      function createUsageChart(topVehicles, topUsers) {
        const ctx = document.getElementById('usageChart').getContext('2d');

        const vehicleLabels = topVehicles.map(v => `${v.make} ${v.model}`);
        const vehicleData = topVehicles.map(v => v.total_usage_days);

        const userLabels = topUsers.map(u => `${u.first_name} ${u.last_name}`);
        const userData = topUsers.map(u => u.total_usage_days);

        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Top 1', 'Top 2', 'Top 3', 'Top 4', 'Top 5'],
            datasets: [{
                label: 'Top Véhicules (en jours d\'utilisation)',
                data: vehicleData,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                tooltips: {
                  labels: vehicleLabels
                }
              },
              {
                label: 'Top Utilisateurs (en jours d\'utilisation)',
                data: userData,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                tooltips: {
                  labels: userLabels
                }
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true
              }
            },
            plugins: {
              tooltip: {
                callbacks: {
                  label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                      label += ': ';
                    }
                    const originalLabels = context.dataset.tooltips.labels;
                    if (context.dataIndex < originalLabels.length) {
                      label += originalLabels[context.dataIndex];
                    }
                    label += ` (${context.parsed.y} jours)`;
                    return label;
                  }
                }
              }
            }
          }
        });
      }

      document.getElementById('logout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        localStorage.removeItem('jwt');
        window.location.href = '/login.php';
      });
    });
  </script>
</body>

</html>