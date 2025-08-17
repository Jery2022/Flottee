<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Employé</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <nav class="navbar">
    <span id="welcome">👋 Bonjour</span>
    <div class="nav-buttons">
      <button onclick="toggleTheme()">🌗 Thème</button>
      <a href="logout.php"><button>🚪 Déconnexion</button></a>
    </div>
  </nav>

  <main>
    <h1>Bienvenue dans le centre d'administration</h1>
    <p id="stats">Chargement des statistiques...</p>
    <canvas id="usageChart" width="400" height="200"></canvas>
    <div id="graph"></div>
  </main>

  <script src="assets/js/theme.js"></script>
  <script>
    const token = localStorage.getItem("jwt_token");

    if (!token || token.split('.').length !== 3) {
        window.location.href = "login.php";
        return;
    }

    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      document.getElementById("welcome").textContent = "👋 Bonjour, " + payload.data.first_name;
    } catch (e) {
      console.error("Erreur de décodage du token :", e);
      localStorage.removeItem("jwt_token");
      window.location.href = "login.php";
      return;
    }

    const user = payload.data;

    if (user.role !== "admin" || user.status !== "active") {
      alert("Accès refusé : vous n'êtes pas autorisé à accéder à cette page.");
      window.location.href = "login.php";
      return;
    }

    document.getElementById("welcome").textContent = "👋 Bonjour, " + user.first_name;

    fetch("http://localhost/api/routes/dashboard", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`,

      }
    })
    .then(res => res.json())
    .then(data => {
      const stats = data.stats;
      document.getElementById("stats").textContent = `Disponibilité: ${stats.disponibilite}%`;

      const ctx = document.getElementById("usageChart").getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: Object.keys(stats.utilisation_mensuelle),
          datasets: [{
            label: "Utilisation mensuelle (%)",
            data: Object.values(stats.utilisation_mensuelle),
            backgroundColor: "rgba(75, 192, 192, 0.6)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      const graph = document.getElementById("graph");
      graph.innerHTML = "<h3>Marques les plus utilisées</h3><ul>";
      for (const [marque, count] of Object.entries(stats.marques_utilisees)) {
        graph.innerHTML += `<li>${marque}: ${count}</li>`;
      }
      graph.innerHTML += "</ul>";

      graph.innerHTML += "<h3>Marques les plus en panne</h3><ul>";
      for (const [marque, count] of Object.entries(stats.marques_en_panne)) {
        graph.innerHTML += `<li>${marque}: ${count}</li>`;
      }
      graph.innerHTML += "</ul>";
    })
    .catch(() => {
      document.getElementById("stats").textContent = "Erreur lors du chargement des statistiques.";
    });
  </script>
</body>
</html>