<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="container py-4 bg-body-secondary">
  <main class="container mt-5 login-container">
    <div class="col-md-12">
      <img src="assets/img/logo/logo-flottee.png" alt="Logo"
        class="d-flex justify-content-center mb-3"
        style="width: 75%; height: 70%; display: block; margin: auto;">
      <h2 class="d-flex justify-content-center">Connexion</h2>
      <?php
      if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
      } ?>
    </div>
    <form method="POST" action="/api/routes/auth/handleForm" id="loginForm" class="col d-flex justify-content-center gap-5 mb-4 mt-5">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
      <div class="d-flex flex-wrap gap-3 justify-content-center">
        <div class="col-md-12">
          <input type="email" name="email" id="email" placeholder="Email" class="form-control" required>
        </div>
        <div class="col-md-12">
          <input type="password" name="password" id="password" placeholder="Mot de passe" class="form-control" required>
        </div>
        <div class="col-md-12">
          <button type="submit" class="btn btn-success w-100">Se connecter</button>
        </div>
      </div>
    </form>
  </main>
</body>

</html>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Récupérer le token CSRF
    fetch('/api/routes/csrf-token')
      .then(res => res.json())
      .then(data => {
        const csrfInput = document.getElementById('csrf_token');
        if (csrfInput) {
          csrfInput.value = data.csrf_token;
        }
      });

    // Gérer la soumission du formulaire
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/api/routes/auth/handleForm', {
            method: 'POST',
            body: formData,
            credentials: 'include'
          })
          .then(res => {
            if (!res.ok) {
              // Si le statut HTTP n'est pas 2xx, on essaie de lire le message d'erreur JSON
              return res.json().then(err => Promise.reject(err));
            }
            return res.json();
          })
          .then(data => {
            if (data.status === 'success' && data.token) {
              // Sauvegarder le token dans le localStorage
              localStorage.setItem('jwt', data.token);
              // Rediriger l'utilisateur
              window.location.href = data.redirect;
            } else {
              // Gérer le cas où le statut est 'success' mais le token est manquant
              alert('Erreur de connexion : token non reçu.');
            }
          })
          .catch(error => {
            // Afficher l'erreur venant du serveur ou une erreur générique
            alert('Erreur : ' + (error.error || 'Identifiants incorrects ou erreur serveur.'));
          });
      });
    }
  });
</script>