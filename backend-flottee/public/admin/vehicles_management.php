<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Véhicules - Flottee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>

<body>
    <div id="main-container">
        <!-- Le contenu du dashboard (navbar, sidebar) sera injecté ici par JS -->
    </div>

    <main class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">Gestion des Véhicules</h1>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-2"></i>Filtres
                </div>
                <div class="card-body">
                    <form id="filter-form" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" id="name-filter" class="form-control" placeholder="Marque ou Modèle">
                        </div>
                        <div class="col-md-2">
                            <select id="type-filter" class="form-select">
                                <option value="">Tous les types</option>
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="truck">Camion</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="status-filter" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="disponible">Disponible</option>
                                <option value="en utilisation">En utilisation</option>
                                <option value="en maintenance">En maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="visibility-filter" class="form-select">
                                <option value="visible">Visible</option>
                                <option value="deleted">Supprimé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filtrer</button>
                            <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Liste des véhicules</span>
                    <button class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter un véhicule</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Marque</th>
                                    <th>Modèle</th>
                                    <th>Immatriculation</th>
                                    <th>Type</th>
                                    <th>Année</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="vehicles-table-body">
                                <!-- Les véhicules seront chargés ici par JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <nav id="pagination-container" aria-label="Pagination des véhicules">
                        <!-- La pagination sera générée ici -->
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('jwt');
            if (!token) {
                window.location.href = '/login.php';
                return;
            }

            fetch('/admin_dashboard.php')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const navbar = doc.querySelector('.navbar');
                    const sidebar = doc.querySelector('.sidebar');
                    document.querySelector('#main-container').prepend(sidebar);
                    document.querySelector('#main-container').prepend(navbar);

                    const scripts = doc.querySelectorAll('script');
                    scripts.forEach(script => {
                        if (script.src) {
                            const newScript = document.createElement('script');
                            newScript.src = script.src;
                            document.body.appendChild(newScript);
                        } else {
                            eval(script.innerText);
                        }
                    });
                })
                .then(() => {
                    const sidebarLinks = document.querySelectorAll('.sidebar a');
                    sidebarLinks.forEach(link => {
                        link.classList.remove('active');
                    });
                    const vehiclesLink = document.querySelector('.sidebar a[href="/admin/vehicles_management.php"]');
                    if (vehiclesLink) {
                        vehiclesLink.classList.add('active');
                    }

                    fetchVehicles(1);

                    document.getElementById('filter-form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        fetchVehicles(1);
                    });

                    document.getElementById('filter-form').addEventListener('reset', function() {
                        setTimeout(() => fetchVehicles(1), 0);
                    });
                });

            function fetchVehicles(page = 1) {
                const tableBody = document.getElementById('vehicles-table-body');
                const name = document.getElementById('name-filter').value;
                const type = document.getElementById('type-filter').value;
                const status = document.getElementById('status-filter').value;
                const visibility = document.getElementById('visibility-filter').value;

                const params = new URLSearchParams({
                    page,
                    name,
                    type,
                    status,
                    visibility
                });

                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Chargement...</td></tr>';

                fetch(`/admin/vehicles?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success' && response.data) {
                            renderVehicles(response.data.vehicles);
                            renderPagination(response.data);
                        } else {
                            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${response.message || 'Erreur lors de la récupération des véhicules.'}</td></tr>`;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Impossible de charger les véhicules.</td></tr>`;
                    });
            }

            function renderVehicles(vehicles) {
                const tableBody = document.getElementById('vehicles-table-body');
                tableBody.innerHTML = '';

                if (vehicles.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun véhicule trouvé.</td></tr>';
                    return;
                }

                vehicles.forEach(vehicle => {
                    const row = `
                        <tr>
                            <td>${vehicle.make}</td>
                            <td>${vehicle.model}</td>
                            <td>${vehicle.license_plate}</td>
                            <td>${vehicle.type}</td>
                            <td>${vehicle.year}</td>
                            <td>${vehicle.status}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Modifier"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }

            function renderPagination(data) {
                const {
                    page,
                    totalPages
                } = data;
                const paginationContainer = document.getElementById('pagination-container');
                paginationContainer.innerHTML = '';

                if (totalPages <= 1) return;

                let paginationHtml = '<ul class="pagination justify-content-center">';

                paginationHtml += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${page - 1}">Précédent</a></li>`;

                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += `<li class="page-item ${i === page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }

                paginationHtml += `<li class="page-item ${page === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${page + 1}">Suivant</a></li>`;

                paginationHtml += '</ul>';
                paginationContainer.innerHTML = paginationHtml;

                paginationContainer.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const newPage = parseInt(this.dataset.page);
                        if (newPage) {
                            fetchVehicles(newPage);
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>