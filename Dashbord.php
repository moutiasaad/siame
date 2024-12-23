<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php'); // Redirect to login page if not user
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
    <title>SIAME Dashboard</title>
    <link rel="stylesheet" href="assets/CSS/Dashbord.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
    <h2 class="logo">
        <a href="http://www.siame.com.tn/">
            <img src="assets/Images/siame.png" alt="SIAME Logo">
        </a>
    </h2>
    <div style="display: flex; align-items: center;">
        <div class="user-info">
            <div class="user-icon">
                <!-- Placeholder for user initials (optional) -->
                <?php 
                // Optional: Display initials if you want them inside the icon
                echo isset($_SESSION['nom']) && isset($_SESSION['prenome']) 
                    ? strtoupper($_SESSION['prenome'][0] . $_SESSION['nom'][0]) 
                    : '';
                ?>
            </div>
            <span class="user-name">
                <?php 
                // Display the user's full name if logged in
                echo isset($_SESSION['nom']) && isset($_SESSION['prenome']) 
                    ? htmlspecialchars($_SESSION['prenome'] . ' ' . $_SESSION['nom']) 
                    : 'Guest';
                ?>
            </span>
        </div>
        <button class="btn logout-btn" onclick="window.location.href='logout.php'">Déconnexion</button>
    </div>
</header>




    <!-- Main Content -->
    <div class="dashboard">
    <?php foreach ($machines as $machine): ?>
    <div class="machine-status">
        <img src="assets/Images/Hercule-h200.png" alt="Machine Image" class="machine-image">
        <div class="machine-info">
            <h3><?php echo htmlspecialchars($machine['nom']); ?></h3>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($machine['location']); ?></p>
            <p class="status <?php echo strtolower($machine['etat']); ?>">
                <strong>Status:</strong> <?php echo ucfirst($machine['etat']); ?>
            </p>
        </div>
        <h3>Temperature & Control</h3>
        <div class="temperature-display">
            <p><strong>Temperature:</strong> <?php echo htmlspecialchars($machine['température']); ?>°C</p>
        </div>
        <label class="switch">
            <input type="checkbox" id="switcher-<?php echo $machine['id']; ?>" 
                   data-id="<?php echo $machine['id']; ?>" 
                   <?php echo ($machine['etat'] === 'on') ? 'checked' : ''; ?>>
            <span class="slider round"></span>
        </label>
        <h3>Production Count</h3>
        <div class="production-count">
            <p><strong>Pieces Produced:</strong> <?php echo htmlspecialchars($machine['pieces_produced']); ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>


    <!-- Scripts -->
    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
    // Handle Switch State Change
    $('input[type="checkbox"]').on('change', function() {
        const machineId = $(this).data('id'); // Get the machine ID from data attribute
        const newState = this.checked ? 'on' : 'off'; // Determine the new state

        $.ajax({
            type: "POST", // Use POST method
            url: 'on-off.php', // Target the updated PHP script
            data: { id: machineId, state: newState }, // Send machine ID and state
            success: function(response) {
                console.log("Switch state updated for Machine ID " + machineId + ": " + response);
            },
            error: function(error) {
                console.error("Error updating switch state for Machine ID " + machineId + ": ", error);
            }
        });
    });
</script>

</body>
</html>
