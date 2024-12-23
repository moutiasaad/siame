<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="assets/CSS/Admin.css">
    <script src="assets/JS/sidebar.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
        <div class="form-container">
        <h1>Add User</h1>
        <form action="add_user_handler.php" method="POST">
            <label for="nom" class="form-label">Last Name</label>
            <input type="text" class="form-control" name="nom" id="nom" required>

            <label for="prenome" class="form-label">First Name</label>
            <input type="text" class="form-control" name="prenome" id="prenome" required>

            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>


            <button type="submit" class="btn-primary">Add User</button>
        </form>
    </div>

</body>
</html>
