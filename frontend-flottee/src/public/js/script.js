
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const vehicleForm = document.getElementById('vehicleForm');
    const vehicleTableBody = document.querySelector('#vehicleTable tbody');
    const loginMessage = document.getElementById('loginMessage');
    const vehicleMessage = document.getElementById('vehicleMessage');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            try {

                fetch('http://localhost/api/routes/users.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({email, password})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('jwt_token', data.token);
                        loginMessage.innerHTML = '<div class="alert alert-success">Connexion réussie</div>';
                        window.location.href = 'vehicles.html';
                    } else {
                        loginMessage.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    }
                });
        
         } catch (error) {
            console.log( data.message || 'Erreur de connexion');
            loginMessage.innerHTML = '<div class="alert alert-danger">'+'Erreur de connexion au serveur'+'</div>'; 
        }
    });
}

    function loadVehicles() {
        fetch('http://localhost/api/routes/vehicles.php')
            .then(res => res.json())
            .then(data => {
                vehicleTableBody.innerHTML = '';
                data.forEach(vehicle => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${vehicle.id}</td>
                        <td>${vehicle.plate}</td>
                        <td>${vehicle.brand}</td>
                        <td>${vehicle.model}</td>
                        <td>${vehicle.year}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editVehicle(${vehicle.id})">Modifier</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteVehicle(${vehicle.id})">Supprimer</button>
                        </td>
                    `;
                    vehicleTableBody.appendChild(row);
                });
            });
    }

    if (vehicleForm) {
        vehicleForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = document.getElementById('vehicleId').value;
            const plate = document.getElementById('plate').value;
            const brand = document.getElementById('brand').value;
            const model = document.getElementById('model').value;
            const year = document.getElementById('year').value;

            const method = id ? 'PUT' : 'POST';
            const url = id ? 'http://localhost/api/routes/vehicles.php?id=' + id : 'http://localhost/api/routes/vehicles.php';

            fetch(url, {
                method: method,
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({plate, brand, model, year})
            })
            .then(res => res.json())
            .then(data => {
                vehicleMessage.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                vehicleForm.reset();
                document.getElementById('vehicleId').value = '';
                loadVehicles();
            });
        });

        window.editVehicle = function (id) {
            fetch('http://localhost/api/routes/vehicles.php?id=' + id)
                .then(res => res.json())
                .then(vehicle => {
                    document.getElementById('vehicleId').value = vehicle.id;
                    document.getElementById('plate').value = vehicle.plate;
                    document.getElementById('brand').value = vehicle.brand;
                    document.getElementById('model').value = vehicle.model;
                    document.getElementById('year').value = vehicle.year;
                });
        };

        window.deleteVehicle = function (id) {
            if (confirm('Supprimer ce véhicule ?')) {
                fetch('http://localhost/api/routes/vehicles.php?id=' + id, {method: 'DELETE'})
                    .then(res => res.json())
                    .then(data => {
                        vehicleMessage.innerHTML = '<div class="alert alert-warning">' + data.message + '</div>';
                        loadVehicles();
                    });
            }
        };

        loadVehicles();
    }
});
