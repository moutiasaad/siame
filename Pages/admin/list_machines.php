<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php'); // Redirect to login page if not admin
    exit();
}

try {
    $stmt = $bdd->query("SELECT * FROM machine");
    $machines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching machines: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Machines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/CSS/Admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1 class="text-center">Liste des Machines</h1>
        <button class="btn-primary-machine" data-bs-toggle="modal" data-bs-target="#addMachineModal">Ajouter une Machine</button>
        <div class="container mt-5">
        <div class="row g-4">
            <?php foreach ($machines as $machine): ?>
            <div class="col-md-12">
                <!-- Machine Card Container -->
                <div class="row g-4">
                    <!-- Machine Overview Card -->
                    <div class="col-md-4">
                        <div class="card shadow-lg border-0 rounded machine-card">
                            <img src="../../assets/Images/Hercule-h200.png" alt="Machine Image" class="img-fluid rounded-top">
                            <div class="card-body text-center">
                                <h4 class="text-primary fw-bold"><?= htmlspecialchars($machine['nom'] ?? 'Non spécifié'); ?></h4>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <?= htmlspecialchars($machine['location'] ?? 'Non spécifié'); ?>
                                </p>
                                <p>
                                    <span class="badge <?= ($machine['etat'] === 'on') ? 'bg-success' : 'bg-danger'; ?>">
                                        <?= ($machine['etat'] === 'on') ? 'Connected' : 'Disconnected'; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Data Overview Card -->
                    <div class="col-md-4">
                        <div class="card shadow-lg border-0 rounded">
                            <div class="card-body">
                                <h5 class="card-title text-center">Machine Data</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Température:</strong> <?= htmlspecialchars($machine['temperature'] ?? '0'); ?>°C</li>
                                    <li><strong>Pression:</strong> <?= htmlspecialchars($machine['pressure'] ?? '0'); ?> Pa</li>
                                    <li><strong>Durée du cycle:</strong> <?= htmlspecialchars($machine['cycle_time'] ?? '0'); ?> s</li>
                                    <li><strong>Pièces Produites:</strong> <?= htmlspecialchars($machine['pieces_produced'] ?? '0'); ?></li>
                                    <li><strong>Consommation d'énergie:</strong> <?= htmlspecialchars($machine['energy_consumption'] ?? '0'); ?> kWh</li>
                                    <li><strong>Tension:</strong> <?= htmlspecialchars($machine['voltage'] ?? '0'); ?> V</li>
                                    <li><strong>Humidité:</strong> <?= htmlspecialchars($machine['humidity'] ?? '0'); ?>%</li>
                                    <li><strong>Dernière synchronisation:</strong> <?= htmlspecialchars($machine['last_sync'] ?? 'N/A'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded control-panel-card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Panneau de contrôle</h5>
                            
                            <!-- State Toggle Switch -->
                           <!-- Power Section Translated to French with Clear Context -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Puissance</span>
                                <label class="switch">
                                    <input type="checkbox" class="state-switch" data-id="<?= $machine['id']; ?>" <?= ($machine['etat'] === 'on') ? 'checked' : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                                <span class="text-muted ms-2">
                                    <?= ($machine['etat'] === 'on') ? 'Allumé' : 'Éteint'; ?>
                                </span>
                            </div>


                            <!-- Delete Button -->
                            <button class="btn btn-danger w-100 delete-btn" data-id="<?= $machine['id']; ?>">Supprimer</button>
                        </div>
                    </div>
                </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>






    <div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="addMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Made modal larger for more fields -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMachineModalLabel">Ajouter une Machine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_machine.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom de la machine</label>
                            <input type="text" class="form-control" name="nom" id="nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Emplacement</label>
                            <input type="text" class="form-control" name="location" id="location" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="temperature" class="form-label">Température (°C)</label>
                            <input type="number" class="form-control" name="temperature" id="temperature" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pressure" class="form-label">Pression (Pa)</label>
                            <input type="number" class="form-control" name="pressure" id="pressure" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cycle_time" class="form-label">Cycle Time (s)</label>
                                <input type="number" class="form-control" name="cycle_time" id="cycle_time" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="energy_consumption" class="form-label">Consommation d'énergie (kWh)</label>
                                <input type="number" class="form-control" name="energy_consumption" id="energy_consumption" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="voltage" class="form-label">Voltage (V)</label>
                                <input type="number" class="form-control" name="voltage" id="voltage" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="current" class="form-label">Courant (A)</label>
                                <input type="number" class="form-control" name="current" id="current" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="humidity" class="form-label">Humidité (%)</label>
                                <input type="number" class="form-control" name="humidity" id="humidity" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="screw_speed" class="form-label">Vitesse de vis (RPM)</label>
                                <input type="number" class="form-control" name="screw_speed" id="screw_speed" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="screw_position" class="form-label">Position de vis</label>
                                <input type="number" class="form-control" name="screw_position" id="screw_position" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="clamping_force" class="form-label">Force de serrage (kN)</label>
                                <input type="number" class="form-control" name="clamping_force" id="clamping_force" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="uptime" class="form-label">Temps de fonctionnement (minutes)</label>
                                <input type="number" class="form-control" name="uptime" id="uptime" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="downtime" class="form-label">Temps d'arrêt (minutes)</label>
                                <input type="number" class="form-control" name="downtime" id="downtime" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cycle_count" class="form-label">Nombre de cycles</label>
                                <input type="number" class="form-control" name="cycle_count" id="cycle_count" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_sync" class="form-label">Dernière synchronisation</label>
                                <input type="datetime-local" class="form-control" name="last_sync" id="last_sync" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ajouter la Machine</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('change', '.state-switch', function() {
            const machineId = $(this).data('id');
            const newState = $(this).is(':checked') ? 'on' : 'off';

            // Send AJAX request to update the machine state
            $.ajax({
                type: "POST",
                url: '../on-off.php', // Your endpoint for toggling the state
                data: { id: machineId, state: newState },
                success: function(response) {
                    // alert("Machine state updated successfully!");
                    location.reload();
                    console.log(response);
                },
                error: function(error) {
                    console.error("Error updating machine state:", error);
                }
            });
        });

                    $(document).on('click', '.delete-btn', function () {
                        const machineId = $(this).data('id');

                        Swal.fire({
                            title: 'Es-tu sûr?',
                            text: "Vous ne pourrez pas revenir en arrière !",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Oui, supprime-le !'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Send AJAX request to delete the machine
                                $.ajax({
                                    type: "POST",
                                    url: 'delete_machine.php', // Your endpoint for deleting a machine
                                    data: { id: machineId },
                                    success: function (response) {
                                        Swal.fire({
                                            title: 'Supprimé!',
                                            text: 'La machine a été supprimée avec succès.',
                                            icon: 'success',
                                            confirmButtonColor: '#28a745',
                                            confirmButtonText: 'D''ACCORD'
                                        }).then(() => {
                                            location.reload(); // Reload the page to reflect changes
                                        });
                                    },
                                    error: function (error) {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: 'Un problème est survenu lors de la suppression de la machine.',
                                            icon: 'error',
                                            confirmButtonColor: '#dc3545',
                                            confirmButtonText: 'OK'
                                        });
                                        console.error("Error deleting machine:", error);
                                    }
                                });
                            }
                        });
                    });

    </script>

</body>
</html>
