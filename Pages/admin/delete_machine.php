<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if (!empty($id)) {
        $stmt = $bdd->prepare("DELETE FROM `machine` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        echo "Machine with ID $id has been deleted successfully.";
    } else {
        echo "Invalid machine ID.";
    }
} else {
    echo "Invalid request method.";
}
?>
