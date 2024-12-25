<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if (!empty($id)) {
        $stmt = $bdd->prepare("DELETE FROM `utlisateur` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        echo "Utilisateur avec l'ID $id a été supprimé avec succès.";
    } else {
        echo "ID utilisateur invalide.";
    }
} else {
    echo "Méthode de requête invalide.";
}
?>
