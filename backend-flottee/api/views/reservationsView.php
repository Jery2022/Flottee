<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des réservations</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="dashboard.php">🏠 Accueil</a>
    <a href="users.php">👥 Utilisateurs</a>
    <a href="reservations.php">🚗 Réservations</a>
    <a href="logout.php"><button>🚪 Déconnexion</button></a>
  </nav>

  <main>
    <h1>🚗 Liste des réservations</h1>
    <input type="text" id="search" placeholder="🔍 Rechercher...">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Utilisateur</th>
          <th>Véhicule</th>
          <th>Début</th>
          <th>Fin</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="reservationTable"></tbody>
    </table>
  </main>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) window.location.href = "login.php";

    async function fetchReservations() {
      const res = await fetch("http://localhost/api/routes/reservations.php", {
        headers: { Authorization: "Bearer " + token }
      });
      const reservations = await res.json();
      renderTable(reservations);
    }

    function renderTable(reservations) {
      const tbody = document.getElementById("reservationTable");
      tbody.innerHTML = "";
      reservations.forEach(r => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${r.id}</td>
          <td>${r.user_id}</td>
          <td>${r.vehicle_id}</td>
          <td>${r.start_date}</td>
          <td>${r.end_date || "—"}</td>
          <td><button onclick="deleteReservation(${r.id})">🗑️ Supprimer</button></td>
        `;
        tbody.appendChild(row);
      });
    }

    async function deleteReservation(id) {
      if (!confirm("Confirmer la suppression ?")) return;
      await fetch(`http://localhost/api/routes/deleteReservation.php?id=${id}`, {
        method: "DELETE",
        headers: { Authorization: "Bearer " + token }
      });
      fetchReservations();
    }

    document.getElementById("search").addEventListener("input", async function () {
      const query = this.value.toLowerCase();
      const res = await fetch("http://localhost/api/routes/reservations.php", {
        headers: { Authorization: "Bearer " + token }
      });
      const reservations = await res.json();
      const filtered = reservations.filter(r =>
        r.user_id.toString().includes(query) ||
        r.vehicle_id.toString().includes(query)
      );
      renderTable(filtered);
    });

    fetchReservations();
  </script>
</body>
</html>
