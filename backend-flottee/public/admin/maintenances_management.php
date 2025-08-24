<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Maintenances - Flottee</title>
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
            <h1 class="mb-4">Gestion des Maintenances</h1>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-2"></i>Filtres
                </div>
                <div class="card-body">
                    <form id="filter-form" class="row g-3">
                        <div class="col-md-3">
                            <select id="severity-filter" class="form-select">
                                <option value="">Toutes les gravités</option>
                                <option value="mineure">Mineure</option>
                                <option value="modérée">Modérée</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="type-filter" class="form-select">
                                <option value="">Tous les types</option>
                                <option value="électrique">Électrique</option>
                                <option value="mécanique">Mécanique</option>
                                <option value="pneumatique">Pneumatique</option>
                                <option value="carrosserie">Carrosserie</option>
                                <option value="entretien courant">Entretien courant</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="status-filter" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="planifiée">Planifiée</option>
                                <option value="en cours">En cours</option>
                                <option value="terminée">Terminée</option>
                                <option value="annulée">Annulée</option>
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
                    <span>Liste des maintenances</span>
                    <button id="add-maintenance-btn" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter une maintenance</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Véhicule</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Gravité</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Coût</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="maintenances-table-body">
                                <!-- Les maintenances seront chargées ici par JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <nav id="pagination-container" aria-label="Pagination des maintenances">
                        <!-- La pagination sera générée ici -->
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="maintenance-modal" tabindex="-1" aria-labelledby="maintenance-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="maintenance-modal-label">Ajouter une maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="maintenance-form">
                        <input type="hidden" id="maintenance_id" name="id">
                        <div class="mb-3">
                            <label for="vehicle_id" class="form-label">Véhicule</label>
                            <select id="vehicle_id" name="vehicle_id" class="form-select" required>
                                <!-- Les véhicules seront chargés ici -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="électrique">Électrique</option>
                                <option value="mécanique">Mécanique</option>
                                <option value="pneumatique">Pneumatique</option>
                                <option value="carrosserie">Carrosserie</option>
                                <option value="entretien courant" selected>Entretien courant</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="planifiée" selected>Planifiée</option>
                                <option value="en cours">En cours</option>
                                <option value="terminée">Terminée</option>
                                <option value="annulée">Annulée</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="severity" class="form-label">Gravité</label>
                            <select id="severity" name="severity" class="form-select" required>
                                <option value="mineure" selected>Mineure</option>
                                <option value="modérée">Modérée</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" id="date" name="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="cost" class="form-label">Coût</label>
                            <input type="number" id="cost" name="cost" class="form-control" step="0.01" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" form="maintenance-form" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('jwt');
            if (!token) {
                window.location.href = '/login.php';
                return;
            }

            let maintenanceModal;

            // Charger le template du dashboard
            fetch('/admin_dashboard.php')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const navbar = doc.querySelector('.navbar');
                    const sidebar = doc.querySelector('.sidebar');
                    document.querySelector('#main-container').prepend(sidebar);
                    document.querySelector('#main-container').prepend(navbar);
                    // Une fois le HTML injecté, on peut initialiser les fonctionnalités
                    initializeTheme();
                    showToggle();
                    logoutAction();

                    // Mettre à jour le lien actif dans la sidebar
                    const sidebarLinks = document.querySelectorAll('.sidebar a');
                    sidebarLinks.forEach(link => link.classList.remove('active'));
                    const maintenancesLink = document.querySelector('.sidebar a[href="/admin/maintenances_management.php"]');
                    if (maintenancesLink) maintenancesLink.classList.add('active');

                    maintenanceModal = new bootstrap.Modal(document.getElementById('maintenance-modal'));

                    loadVehicles();
                    fetchMaintenances(1);

                    document.getElementById('filter-form').addEventListener('submit', e => {
                        e.preventDefault();
                        fetchMaintenances(1);
                    });

                    document.getElementById('filter-form').addEventListener('reset', () => {
                        setTimeout(() => fetchMaintenances(1), 0);
                    });

                    document.getElementById('add-maintenance-btn').addEventListener('click', () => {
                        document.getElementById('maintenance-form').reset();
                        document.getElementById('maintenance_id').value = '';
                        document.getElementById('maintenance-modal-label').textContent = 'Ajouter une maintenance';
                        maintenanceModal.show();
                    });

                    document.getElementById('maintenance-form').addEventListener('submit', handleFormSubmit);
                    document.getElementById('maintenances-table-body').addEventListener('click', handleTableClick);

                    // Récupérer les informations de l'utilisateur pour le message de bienvenue
                    return fetch('/admin/dashboard', {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.user && data.user.first_name) {
                        const welcomeMessage = document.getElementById('welcome-message');
                        if (welcomeMessage) {
                            welcomeMessage.textContent = `Bienvenue, ${data.user.first_name}`;
                        }
                    }
                })
                .catch(error => {
                    console.error("Erreur lors du chargement du dashboard ou des informations utilisateur:", error);
                });

            function loadVehicles() {
                fetch('/api/routes/vehicles', {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success') {
                            const vehicleSelect = document.getElementById('vehicle_id');
                            vehicleSelect.innerHTML = '';
                            response.data.vehicles.forEach(vehicle => {
                                const option = `<option value="${vehicle.id}">${vehicle.make} ${vehicle.model} (${vehicle.license_plate})</option>`;
                                vehicleSelect.innerHTML += option;
                            });
                        }
                    });
            }

            function fetchMaintenances(page = 1) {
                const tableBody = document.getElementById('maintenances-table-body');
                const type = document.getElementById('type-filter').value;
                const status = document.getElementById('status-filter').value;
                const severity = document.getElementById('severity-filter').value;
                const params = new URLSearchParams({
                    page,
                    type: type,
                    status: status,
                    severity: severity
                });

                tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Chargement...</td></tr>';

                fetch(`/api/routes/maintenances?${params.toString()}`, {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success' && response.data) {
                            renderMaintenances(response.data.maintenances);
                            renderPagination(response.data);
                        } else {
                            tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${response.message || 'Erreur.'}</td></tr>`;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Impossible de charger les maintenances.</td></tr>`;
                    });
            }

            function renderMaintenances(maintenances) {
                const tableBody = document.getElementById('maintenances-table-body');
                tableBody.innerHTML = '';

                if (maintenances.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Aucune maintenance trouvée.</td></tr>';
                    return;
                }

                maintenances.forEach(m => {
                    const row = `
                        <tr data-id="${m.id}">
                            <td>${m.make} ${m.model} (${m.license_plate})</td>
                            <td>${m.type}</td>
                            <td>${m.status}</td>
                            <td>${m.severity}</td>
                            <td>${m.description}</td>
                            <td>${m.date}</td>
                            <td>${m.cost} €</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-btn" title="Modifier"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" title="Supprimer"><i class="fas fa-trash"></i></button>
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
                        if (newPage) fetchMaintenances(newPage);
                    });
                });
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                const form = e.target;
                const id = form.querySelector('#maintenance_id').value;
                const data = Object.fromEntries(new FormData(form).entries());

                const url = id ? `/api/routes/maintenances/${id}` : '/api/routes/maintenances';
                const method = id ? 'PUT' : 'POST';

                fetch(url, {
                        method: method,
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success') {
                            maintenanceModal.hide();
                            fetchMaintenances();
                        } else {
                            alert('Erreur: ' + response.message);
                        }
                    });
            }

            function handleTableClick(e) {
                const target = e.target.closest('button');
                if (!target) return;

                const row = target.closest('tr');
                const id = row.dataset.id;

                if (target.classList.contains('edit-btn')) {
                    fetch(`/api/routes/maintenances/${id}`, {
                            headers: {
                                'Authorization': `Bearer ${token}`
                            }
                        })
                        .then(response => response.json())
                        .then(response => {
                            if (response.status === 'success') {
                                const form = document.getElementById('maintenance-form');
                                form.querySelector('#maintenance_id').value = response.data.id;
                                form.querySelector('#vehicle_id').value = response.data.vehicle_id;
                                form.querySelector('#type').value = response.data.type;
                                form.querySelector('#status').value = response.data.status;
                                form.querySelector('#severity').value = response.data.severity;
                                form.querySelector('#description').value = response.data.description;
                                form.querySelector('#date').value = response.data.date;
                                form.querySelector('#cost').value = response.data.cost;
                                document.getElementById('maintenance-modal-label').textContent = 'Modifier la maintenance';
                                maintenanceModal.show();
                            }
                        });
                }

                if (target.classList.contains('delete-btn')) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer cette maintenance ?')) {
                        fetch(`/api/routes/maintenances/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Authorization': `Bearer ${token}`
                                }
                            })
                            .then(response => response.json())
                            .then(response => {
                                if (response.status === 'success') {
                                    fetchMaintenances();
                                } else {
                                    alert('Erreur: ' + response.message);
                                }
                            });
                    }
                }
            }
        });
    </script>
</body>

</html>