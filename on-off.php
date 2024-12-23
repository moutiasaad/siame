<?php
require_once 'db.php'; // Ensure the database connection is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // Retrieve the machine ID from the POST request
    $state = $_POST['state']; // Retrieve the new state ('on' or 'off')

    // Validate the inputs
    if (!empty($id) && in_array($state, ['on', 'off'])) {
        // Update the machine state in the database
        $stmt = $bdd->prepare("UPDATE `machine` SET `etat` = :state WHERE `id` = :id");
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
?>
