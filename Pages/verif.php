<?php
session_start();

// Include the database connection
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $bdd->prepare("SELECT * FROM `utlisateur` WHERE `email` = :email AND `mdp` = :password");
    $stmt->execute([
        ':email' => $user,
        ':password' => $password
    ]);

    $userData = $stmt->fetch();

    if ($userData) {
        // Store the user's data in the session
        $_SESSION['email'] = $userData['email'];      // Email
        $_SESSION['nom'] = $userData['nom'];          // Last Name
        $_SESSION['prenome'] = $userData['prenome'];  // First Name
        $_SESSION['role'] = $userData['role'];        // Role (admin or user)

        // Redirect based on role
        if ($userData['role'] === 'admin') {
            header('Location: admin/list_machines.php'); // Redirect to admin page
        } else {
            header('Location: user/Dashbord.php'); // Redirect to user dashboard
        }
        exit();
    } else {
        $_SESSION['error'] = 'Incorrect email or password.';
        header('Location: ../index.php'); // Redirect back to the login page
        exit();
    }
}
?>
