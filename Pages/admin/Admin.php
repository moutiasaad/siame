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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/CSS/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#machines"><i class="fas fa-cogs"></i> Machines</a></li>
            <li><a href="#add-machine"><i class="fas fa-plus-circle"></i> Add Machine</a></li>
            <li><a href="#add-user"><i class="fas fa-user-plus"></i> Add User</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="shortcuts">
            <h6>Shortcuts</h6>
            <ul>
                <li><a href="#analytics"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="#messages"><i class="fas fa-envelope"></i> Messages</a></li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Header -->
        <header class="navbar">
            <h2 class="logo">My Dashboard</h2>
            <div class="user-info">
                <div class="user-icon">
                    <?php 
                    echo isset($_SESSION['nom']) && isset($_SESSION['prenome']) 
                        ? strtoupper($_SESSION['prenome'][0] . $_SESSION['nom'][0]) 
                        : '';
                    ?>
                </div>
                <span class="user-name">
                    <?php 
                    echo isset($_SESSION['nom']) && isset($_SESSION['prenome']) 
                        ? htmlspecialchars($_SESSION['prenome'] . ' ' . $_SESSION['nom']) 
                        : 'Guest';
                    ?>
                </span>
            </div>
        </header>

        <!-- Machines Section -->
        <section id="machines">
            <h3>Machines</h3>
            <div class="machines-grid">
            <div class="dashboard">
                    <?php foreach ($machines as $machine): ?>
                    <!-- Machine Status Section -->
                    <div class="machine-status">
                        <img src="../../assets/Images/Hercule-h200.png" alt="Machine Image" class="machine-image">
                        <div class="machine-info">
                            <h3><?php echo htmlspecialchars($machine['nom']); ?></h3>
                            <p><strong>Location:</strong><?php echo htmlspecialchars($machine['location']); ?></p>
                            <p class="status <?php echo strtolower($machine['etat']); ?>">
                                <strong>Status:</strong> <?php echo ucfirst($machine['etat']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Temperature & Switch Control Section -->
                    <div class="control-panel">
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
                    </div>

                    <!-- Production Count Section -->
                    <div class="oee-section">
                        <h3>Production Count</h3>
                        <div class="production-count">
                            <p><strong>Pieces Produced:</strong> <?php echo htmlspecialchars($machine['pieces_produced']); ?></p>
                        </div>
                    </div>

                    <!-- Report Section -->
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Add Machine Form -->
        <section id="add-machine">
            <h3>Add Machine</h3>
            <form action="add_machine.php" method="POST" class="form-container">
                <div class="mb-3">
                    <label for="nom" class="form-label">Machine Name</label>
                    <input type="text" class="form-control" name="nom" id="nom" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" id="location" required>
                </div>
                <div class="mb-3">
                    <label for="temperature" class="form-label">Temperature</label>
                    <input type="number" class="form-control" name="temperature" id="temperature" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Machine</button>
            </form>
        </section>

        <!-- Add User Form -->
        <section id="add-user">
            <h3>Add User</h3>
            <form action="add_user.php" method="POST" class="form-container">
                <div class="mb-3">
                    <label for="nom" class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="nom" id="nom" required>
                </div>
                <div class="mb-3">
                    <label for="prenome" class="form-label">First Name</label>
                    <input type="text" class="form-control" name="prenome" id="prenome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Add User</button>
            </form>
        </section>
    </div>

    <script>
        // Toggle Sidebar
        document.querySelector('.toggle-btn').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>
