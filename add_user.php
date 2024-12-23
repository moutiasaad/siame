<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenome = $_POST['prenome'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $bdd->prepare("INSERT INTO utlisateur (nom, prenome, email, mdp) VALUES (:nom, :prenome, :email, :password)");
    $stmt->execute([
        ':nom' => $nom,
        ':prenome' => $prenome,
        ':email' => $email,
        ':password' => password_hash($password, PASSWORD_BCRYPT) // Encrypt password
    ]);

    header('Location: Dashbord.php');
    exit();
}
?>
