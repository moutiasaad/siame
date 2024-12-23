<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php'); // Redirect to login page if not admin
    exit();
}
$stmt = $bdd->query("SELECT * FROM machine");
$machines = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>liste des machines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/CSS/Admin.css">
    <script src="../../assets/JS/sidebar.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div>
        <h1 class="table-container">liste des machines</h1>
        <button class="btn-primary-machine" data-bs-toggle="modal" data-bs-target="#addMachineModal">Ajouter une machine</button>
        </div>  
    <div class="main-content">      
        <div class="dashboard">
            <?php foreach ($machines as $machine): ?>
            <div class="machine-status">
                <img src="../../assets/Images/Hercule-h200.png" alt="Machine Image" class="machine-image">
                <div class="machine-info">
                    <h3><?php echo htmlspecialchars($machine['nom']); ?></h3>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($machine['location']); ?></p>
                    <p class="status <?php echo strtolower($machine['etat']); ?>">
                        <strong>Status:</strong> <?php echo ucfirst($machine['etat']); ?>
                    </p>
                    <h3>Temperature & Control</h3>
                    <div class="temperature-display">
                        <p><strong>Temperature:</strong> <?php echo htmlspecialchars($machine['température']); ?>°C</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" class="state-switch" data-id="<?php echo $machine['id']; ?>" <?php echo ($machine['etat'] === 'on') ? 'checked' : ''; ?>>
                        <span class="slider round"></span>
                    </label>
                    <h3>Production Count</h3>
                    <div class="production-count">
                        <p><strong>Pieces Produced:</strong> <?php echo htmlspecialchars($machine['pieces_produced']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="addMachineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMachineModalLabel">Ajouter une Machine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_machine.php" method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la machine</label>
                            <input type="text" class="form-control" name="nom" id="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Emplacement</label>
                            <input type="text" class="form-control" name="location" id="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="temperature" class="form-label">Température</label>
                            <input type="number" class="form-control" name="temperature" id="temperature" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).on('change', '.state-switch', function() {
            const machineId = $(this).data('id'); // Get the machine ID
            const newState = $(this).is(':checked') ? 'on' : 'off'; // Determine the new state

            // Send AJAX request to update the state
            $.ajax({
                type: "POST",
                url: '../on-off.php',
                data: { id: machineId, state: newState },
                success: function(response) {
                    console.log(response);
                },
                error: function(error) {
                    console.error("Error updating machine state:", error);
                }
            });
        });
    </script>
</body>
</html>
