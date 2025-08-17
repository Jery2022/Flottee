<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des utilisateurs</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="dashboard.php">🏠 Accueil</a>
    <a href="users.php">👥 Utilisateurs</a>
    <a href="logout.php"><button>🚪 Déconnexion</button></a>
  </nav>

  <main>
    <h1>👥 Liste des utilisateurs</h1>
    <input type="text" id="search" placeholder="🔍 Rechercher...">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Pseudo</th>
          <th>Email</th>
          <th>Rôle</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="userTable"></tbody>
    </table>
  </main>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) window.location.href = "login.php";

    async function fetchUsers() {
      const res = await fetch("http://localhost/api/routes/users.php", {
        headers: { Authorization: "Bearer " + token }
      });
      const users = await res.json();
      renderTable(users);
    }

    function renderTable(users) {
      const tbody = document.getElementById("userTable");
      tbody.innerHTML = "";
      users.forEach(user => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${user.id}</td>
          <td>${user.pseudo}</td>
          <td>${user.email}</td>
          <td>${user.role}</td>
          <td><button onclick="deleteUser(${user.id})">🗑️ Supprimer</button></td>
        `;
        tbody.appendChild(row);
      });
    }

    async function deleteUser(id) {
      if (!confirm("Confirmer la suppression ?")) return;
      await fetch(`http://localhost/api/routes/deleteUser.php?id=${id}`, {
        method: "DELETE",
        headers: { Authorization: "Bearer " + token }
      });
      fetchUsers();
    }

    document.getElementById("search").addEventListener("input", async function () {
      const query = this.value.toLowerCase();
      const res = await fetch("http://localhost/api/routes/users.php", {
        headers: { Authorization: "Bearer " + token }
      });
      const users = await res.json();
      const filtered = users.filter(u =>
        u.pseudo.toLowerCase().includes(query) || u.email.toLowerCase().includes(query)
      );
      renderTable(filtered);
    });

    fetchUsers();
  </script>
</body>
</html>
