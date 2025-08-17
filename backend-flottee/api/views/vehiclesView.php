<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des véhicules</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="dashboard.php">🏠 Accueil</a>
    <a href="users.php">👥 Utilisateurs</a>
    <a href="reservations.php">🚗 Réservations</a>
    <a href="vehicles.php">🚙 Véhicules</a>
    <a href="logout.php"><button>🚪 Déconnexion</button></a>
  </nav>

  <main>
    <h1>🚙 Liste des véhicules</h1>
    <input type="text" id="search" placeholder="🔍 Rechercher...">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Marque</th>
          <th>Modèle</th>
          <th>Année</th>
          <th>Immatriculation</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="vehicleTable"></tbody>
    </table>
  </main>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) window.location.href = "login.php";

    async function fetchVehicles() {
      const res = await fetch("http://localhost/api/routes/vehicles.php", {
        headers: { Authorization: "Bearer " + token }
      });
      const vehicles = await res.json();
      renderTable(vehicles);
    }

    function renderTable(vehicles) {
      const tbody = document.getElementById("vehicleTable");
      tbody.innerHTML = "";
      vehicles.forEach(v => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${v.id}</td>
          <td>${v.make}</td>
          <td>${v.model}</td>
          <td>${v.year}</td>
          <td>${v.license_plate}</td>
          <td>${v.status}</td>
          <td><button onclick="deleteVehicle(${v.id})">🗑️ Supprimer</button></td>
        `;
        tbody.appendChild(row);
      });
    }

    async function deleteVehicle(id) {
      if (!confirm("Confirmer la suppression ?")) return;
      await fetch(`http://localhost/api/routes/deleteVehicle.php?id=${id}`, {
        method: "DELETE",
        headers: { Authorization: "Bearer " + token }
      });
      fetchVehicles();
    }

    document.getElementById("search").addEventListener("input", async function () {
      const query = this.value.toLowerCase();
      const res = await fetch("http://localhost/api/routes/vehicles.php", {
        headers: { Authorization: "Bearer " + token }
      });
      const vehicles = await res.json();
      const filtered = vehicles.filter(v =>
        v.make.toLowerCase().includes(query) ||
        v.model.toLowerCase().includes(query) ||
        v.license_plate.toLowerCase().includes(query)
      );
      renderTable(filtered);
    });

    fetchVehicles();
  </script>
</body>
</html>
