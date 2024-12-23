<?php
require_once 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // Get machine ID
    $state = $_POST['state']; // Get new state (on/off)

    // Validate input
    if (!empty($id) && in_array($state, ['on', 'off'])) {
        // Update machine state in the database
        $stmt = $bdd->prepare("UPDATE machine SET etat = :state WHERE id = :id");
        $stmt->execute([
            ':state' => $state,
            ':id' => $id
        ]);

        echo "Machine state updated successfully to $state for machine ID $id.";
    } else {
        echo "Invalid data received.";
    }
} else {
    echo "Invalid request method.";
}
