<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $location = $_POST['location'];
    $temperature = $_POST['temperature'];

    $stmt = $bdd->prepare("INSERT INTO machine (nom, location, température, etat, pieces_produced) VALUES (:nom, :location, :temperature, 'off', 0)");
    $stmt->execute([
        ':nom' => $nom,
        ':location' => $location,
        ':temperature' => $temperature
    ]);

    header('Location: Dashbord.php');
    exit();
}
?>
