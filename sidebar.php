<?php

require_once 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not user
    exit();
}


// Fetch all machines from the database
?>
<aside class="sidebar">
    <!-- Profile Section -->
    <div class="sidebar-header">
        <img src="assets/Images/user.png" alt="Profile Picture">
        <h3>
            <?php 
                // Display the user's full name if logged in
                echo isset($_SESSION['nom']) && isset($_SESSION['prenome']) 
                    ? htmlspecialchars($_SESSION['prenome'] . ' ' . $_SESSION['nom']) 
                    : 'Admin';
            ?>
        </h3>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        <li>
            <a href="list_machines.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'list_machines.php' ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> <span>liste des machines</span>
            </a>
        </li>
        <li>
            <a href="list_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'list_users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> <span>Liste des utilisateurs</span>
            </a>
        </li>
    </ul>

    <!-- Logout Button -->
    <div class="logout">
        <button class="logout-btn" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
    </div>

    <!-- Teams Section -->
    <div class="teams">
        <div class="icon">
            <h2 class="logo">
                <a href="http://www.siame.com.tn/">
                    <img src="assets/Images/siame.png" alt="SIAME Logo">
                </a>
            </h2>
        </div>
    </div>
</aside>

