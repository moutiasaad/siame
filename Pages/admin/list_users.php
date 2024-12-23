<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}

// Fetch all User from the database
$stmt = $bdd->query("SELECT * FROM utlisateur");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Users</title>
    <link rel="stylesheet" href="../../assets/CSS/Admin.css">
    <script src="assets/JS/sidebar.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
        
        <h1 class="table-container">Liste des utilisateurs</h1>
        <div class="table-container">
        <button class="btn-primary-machine" onclick="window.location.href='add_user1.php'">Ajouter un utilisateur</button>
        <table class="table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['prenome'] . ' ' . $user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="table-actions">
                        <button class="btn-danger">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>

</body>
</html>
