<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}

// Fetch all machines from the database
$stmt = $bdd->query("SELECT * FROM machine");
$machines = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Machine</title>
    <link rel="stylesheet" href="../../assets/CSS/Admin.css">
    <script src="assets/JS/sidebar.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="form-container">
            <h1>Add Machine</h1>
            <form action="add_machine_handler.php" method="POST">
                <label for="nom" class="form-label">Machine Name</label>
                <input type="text" class="form-control" name="nom" id="nom" required>

                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" name="location" id="location" required>

                <label for="temperature" class="form-label">Temperature</label>
                <input type="number" class="form-control" name="temperature" id="temperature" step="0.01" required>

                <button type="submit" class="btn-primary">Add Machine</button>
            </form>
        </div>
    </div>
</body>
</html>
