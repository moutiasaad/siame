<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $location = $_POST['location'];
    $temperature = $_POST['temperature'];
    $pressure = $_POST['pressure'];
    $cycle_time = $_POST['cycle_time'];
    $energy_consumption = $_POST['energy_consumption'];
    $voltage = $_POST['voltage'];
    $current = $_POST['current'];
    $humidity = $_POST['humidity'];
    $screw_speed = $_POST['screw_speed'];
    $screw_position = $_POST['screw_position'];
    $clamping_force = $_POST['clamping_force'];
    $uptime = $_POST['uptime'];
    $downtime = $_POST['downtime'];
    $cycle_count = $_POST['cycle_count'];
    $last_sync = $_POST['last_sync'];

    try {
        $stmt = $bdd->prepare("INSERT INTO machine (
            nom, location, temperature, pressure, cycle_time, energy_consumption, voltage, current, humidity, 
            screw_speed, screw_position, clamping_force, uptime, downtime, cycle_count, last_sync, etat
        ) VALUES (
            :nom, :location, :temperature, :pressure, :cycle_time, :energy_consumption, :voltage, :current, :humidity, 
            :screw_speed, :screw_position, :clamping_force, :uptime, :downtime, :cycle_count, :last_sync, 'off'
        )");

        $stmt->execute([
            ':nom' => $nom,
            ':location' => $location,
            ':temperature' => $temperature,
            ':pressure' => $pressure,
            ':cycle_time' => $cycle_time,
            ':energy_consumption' => $energy_consumption,
            ':voltage' => $voltage,
            ':current' => $current,
            ':humidity' => $humidity,
            ':screw_speed' => $screw_speed,
            ':screw_position' => $screw_position,
            ':clamping_force' => $clamping_force,
            ':uptime' => $uptime,
            ':downtime' => $downtime,
            ':cycle_count' => $cycle_count,
            ':last_sync' => $last_sync
        ]);

        header('Location: list_machines.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
