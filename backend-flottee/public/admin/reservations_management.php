<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations - Flottee</title>
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
            <h1 class="mb-4">Gestion des Réservations</h1>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-2"></i>Filtres
                </div>
                <div class="card-body">
                    <form id="filter-form" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" id="vehicle-filter" class="form-control" placeholder="Véhicule (ID, Plaque)">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="user-filter" class="form-control" placeholder="Utilisateur (ID, Nom)">
                        </div>
                        <div class="col-md-2">
                            <select id="status-filter" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="confirmée">Confirmée</option>
                                <option value="en attente">En attente</option>
                                <option value="annulée">Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Filtrer</button>
                            <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Liste des réservations</span>
                    <button id="add-reservation-btn" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter une réservation</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Véhicule</th>
                                    <th>Utilisateur</th>
                                    <th>Date de début</th>
                                    <th>Date de fin</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reservations-table-body">
                                <!-- Les réservations seront chargées ici par JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <nav id="pagination-container" aria-label="Pagination des réservations">
                        <!-- La pagination sera générée ici -->
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal for Add/Edit Reservation -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Ajouter une Réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reservation-form">
                        <input type="hidden" id="reservation-id">
                        <div class="mb-3">
                            <label for="vehicle-id" class="form-label">Véhicule</label>
                            <select class="form-select" id="vehicle-id" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="user-id" class="form-label">Utilisateur</label>
                            <select class="form-select" id="user-id" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="start-date" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="start-date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end-date" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="end-date" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status">
                                <option value="confirmed">Confirmée</option>
                                <option value="pending">En attente</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" form="reservation-form" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

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
                    const reservationsLink = document.querySelector('.sidebar a[href="/admin/reservations_management.php"]');
                    if (reservationsLink) {
                        reservationsLink.classList.add('active');
                    }

                    fetchReservations(1);

                    document.getElementById('filter-form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        fetchReservations(1);
                    });

                    document.getElementById('filter-form').addEventListener('reset', function() {
                        setTimeout(() => fetchReservations(1), 0);
                    });

                    const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));

                    document.getElementById('add-reservation-btn').addEventListener('click', function() {
                        document.getElementById('reservation-form').reset();
                        document.getElementById('reservation-id').value = '';
                        document.getElementById('reservationModalLabel').textContent = 'Ajouter une Réservation';
                        loadSelectOptions();
                        reservationModal.show();
                    });

                    document.getElementById('reservation-form').addEventListener('submit', handleFormSubmit);
                });

            function fetchReservations(page = 1) {
                const tableBody = document.getElementById('reservations-table-body');
                const vehicle = document.getElementById('vehicle-filter').value;
                const user = document.getElementById('user-filter').value;
                const status = document.getElementById('status-filter').value;

                const params = new URLSearchParams({
                    page,
                    vehicle,
                    user,
                    status
                });

                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Chargement...</td></tr>';

                fetch(`/admin/reservations?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success' && response.data) {
                            renderReservations(response.data.reservations);
                            renderPagination(response.data);
                        } else {
                            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${response.message || 'Erreur lors de la récupération des réservations.'}</td></tr>`;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Impossible de charger les réservations.</td></tr>`;
                    });
            }

            function renderReservations(reservations) {
                const tableBody = document.getElementById('reservations-table-body');
                tableBody.innerHTML = '';

                if (reservations.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Aucune réservation trouvée.</td></tr>';
                    return;
                }

                reservations.forEach(reservation => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${reservation.vehicle_info}</td>
                        <td>${reservation.user_info}</td>
                        <td>${new Date(reservation.start_date).toLocaleDateString()}</td>
                        <td>${new Date(reservation.end_date).toLocaleDateString()}</td>
                        <td>${reservation.status}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" title="Modifier"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" title="Supprimer"><i class="fas fa-trash"></i></button>
                        </td>
                    `;

                    row.querySelector('.edit-btn').addEventListener('click', () => editReservation(reservation));
                    row.querySelector('.delete-btn').addEventListener('click', () => deleteReservation(reservation.id));

                    tableBody.appendChild(row);
                });
            }

            function editReservation(reservation) {
                const form = document.getElementById('reservation-form');
                form.reset();

                document.getElementById('reservation-id').value = reservation.id;
                document.getElementById('reservationModalLabel').textContent = 'Modifier la Réservation';

                // Format dates correctly for input type="date"
                const startDate = new Date(reservation.start_date).toISOString().split('T')[0];
                const endDate = new Date(reservation.end_date).toISOString().split('T')[0];

                document.getElementById('start-date').value = startDate;
                document.getElementById('end-date').value = endDate;
                document.getElementById('status').value = reservation.status;

                const reservationModal = bootstrap.Modal.getInstance(document.getElementById('reservationModal')) || new bootstrap.Modal(document.getElementById('reservationModal'));

                loadSelectOptions().then(() => {
                    document.getElementById('vehicle-id').value = reservation.vehicle_id;
                    document.getElementById('user-id').value = reservation.user_id;
                    reservationModal.show();
                });
            }

            function deleteReservation(id) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')) {
                    return;
                }

                fetch(`/api/routes/reservations/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('jwt')}`
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            fetchReservations(); // Refresh list
                        } else {
                            alert('Erreur: ' + result.message);
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                const id = document.getElementById('reservation-id').value;
                const data = {
                    vehicle_id: document.getElementById('vehicle-id').value,
                    user_id: document.getElementById('user-id').value,
                    start_date: document.getElementById('start-date').value,
                    end_date: document.getElementById('end-date').value,
                    status: document.getElementById('status').value,
                };

                const url = id ? `/api/routes/reservations/${id}` : '/api/routes/reservations';
                const method = id ? 'PUT' : 'POST';

                fetch(url, {
                        method: method,
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('jwt')}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            bootstrap.Modal.getInstance(document.getElementById('reservationModal')).hide();
                            fetchReservations(); // Refresh the list
                        } else {
                            alert('Erreur: ' + result.message);
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }

            function loadSelectOptions() {
                const token = localStorage.getItem('jwt');
                const headers = {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                };

                const vehiclePromise = fetch('/admin/vehicles', {
                        headers
                    })
                    .then(res => res.json())
                    .then(data => {
                        const vehicleSelect = document.getElementById('vehicle-id');
                        vehicleSelect.innerHTML = '<option value="">Sélectionnez un véhicule</option>';
                        if (data.data && data.data.vehicles) {
                            data.data.vehicles.forEach(v => {
                                vehicleSelect.innerHTML += `<option value="${v.id}">${v.make} ${v.model} (${v.license_plate})</option>`;
                            });
                        }
                    });

                const userPromise = fetch('/admin/users', {
                        headers
                    })
                    .then(res => res.json())
                    .then(data => {
                        const userSelect = document.getElementById('user-id');
                        userSelect.innerHTML = '<option value="">Sélectionnez un utilisateur</option>';
                        if (data.data && data.data.users) {
                            data.data.users.forEach(u => {
                                userSelect.innerHTML += `<option value="${u.id}">${u.first_name} ${u.last_name}</option>`;
                            });
                        }
                    });

                return Promise.all([vehiclePromise, userPromise]);
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
                            fetchReservations(newPage);
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>