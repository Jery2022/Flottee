<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrateur - Flottee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
    <div class="container-fluid">
      <!-- Toggle button for mobile -->
      <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>

      <a class="navbar-brand ms-2" href="#">
        <img src="/assets/img/logo/logo-flottee.png" alt="Flottee Logo">
      </a>

      <div class="ms-auto d-flex align-items-center">
        <span id="welcome-message" class="navbar-text me-3 d-none d-sm-block"></span>
        <button class="btn btn-outline-secondary me-2" id="theme-toggle" title="Changer de thème">
          <i class="fas fa-moon"></i>
        </button>
        <div class="dropdown">
          <button class="btn btn-outline-primary" type="button" id="profileMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileMenuButton">
            <li><a class="dropdown-item" href="#">Mon Profil</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#" id="logout-link-navbar">Déconnexion</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <div class="sidebar">
    <a href="/admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
    <a href="/admin/vehicles_management.php"><i class="fas fa-car me-2"></i>Véhicules</a>
    <a href="/admin/users_management.php"><i class="fas fa-users me-2"></i>Utilisateurs</a>
    <a href="#"><i class="fas fa-tools me-2"></i>Maintenance</a>
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

      let usageChart = null;
      let chartData = {};

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
            chartData.topVehicles = data.stats.top_used_vehicles;
            chartData.topUsers = data.stats.top_users;
            createUsageChart();
            if (data.user && data.user.first_name) {
              document.getElementById('welcome-message').textContent = `Bienvenue, ${data.user.first_name}`;
            }
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

      function createUsageChart() {
        if (usageChart) {
          usageChart.destroy();
        }

        if (!chartData.topVehicles || !chartData.topUsers) {
          return;
        }

        const ctx = document.getElementById('usageChart').getContext('2d');
        const isDarkMode = document.body.classList.contains('dark-theme');
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDarkMode ? '#e0e0e0' : '#000';

        const vehicleLabels = chartData.topVehicles.map(v => `${v.make} ${v.model}`);
        const vehicleData = chartData.topVehicles.map(v => v.total_usage_days);

        const userLabels = chartData.topUsers.map(u => `${u.first_name} ${u.last_name}`);
        const userData = chartData.topUsers.map(u => u.total_usage_days);

        usageChart = new Chart(ctx, {
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
                beginAtZero: true,
                grid: {
                  color: gridColor
                },
                ticks: {
                  color: textColor
                }
              },
              x: {
                grid: {
                  color: gridColor
                },
                ticks: {
                  color: textColor
                }
              }
            },
            plugins: {
              legend: {
                labels: {
                  color: textColor
                }
              },
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

      document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('show');
      });

      // Theme toggle
      const themeToggle = document.getElementById('theme-toggle');
      themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-theme');
        const isDarkMode = document.body.classList.contains('dark-theme');
        localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
        themeToggle.innerHTML = isDarkMode ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        createUsageChart();
      });

      // Apply saved theme
      if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-theme');
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
      }

      // Logout from navbar
      document.getElementById('logout-link-navbar').addEventListener('click', function(e) {
        e.preventDefault();
        localStorage.removeItem('jwt');
        window.location.href = '/login.php';
      });
    });
  </script>
</body>

</html>