<?php
// db.php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=siame', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
