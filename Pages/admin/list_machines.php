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
    <link rel="stylesheet" href="../../assets/CSS/Admin.css">
    <script src="../../assets/JS/sidebar.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div>
        <h1 class="table-container">liste des machines</h1>
        <button class="btn-primary-machine" onclick="window.location.href='add_machine1.php'">Ajouter une machine</button>
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
                        <input type="checkbox" data-id="<?php echo $machine['id']; ?>" <?php echo ($machine['etat'] === 'on') ? 'checked' : ''; ?>>
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


</body>
</html>
