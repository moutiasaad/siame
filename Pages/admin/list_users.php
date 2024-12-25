<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}

// Fetch all User from the database
$stmt = $bdd->query("SELECT * FROM utlisateur");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="../../assets/CSS/Admin.css">
    <script src="assets/JS/sidebar.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
        
        <h1 class="table-container">Liste des utilisateurs</h1>
        <div class="table-container">
        <button class="btn-primary-machine" data-bs-toggle="modal" data-bs-target="#addUserModal">Ajouter un utilisateur</button>
        <table class="table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['prenome'] . ' ' . $user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <button class="btn btn-danger delete-user" data-id="<?php echo $user['id']; ?>">Supprimer</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>
       <!-- Modal -->
       <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_user.php" method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" id="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenome" class="form-label">Prénom</label>
                            <input type="text" class="form-control" name="prenome" id="prenome" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe </label>
                            <input id="password" class="form-control" type="password" name="password" placeholder="Entrez mot de passe ici"  required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        let userIdToDelete = null;

        // Show confirmation modal
        $(document).on('click', '.delete-user', function() {
            userIdToDelete = $(this).data('id'); // Store user ID to delete
            $('#deleteUserModal').modal('show'); // Show the confirmation modal
        });

        // Handle confirm delete action
        $('#confirmDeleteUser').on('click', function() {
            if (userIdToDelete) {
                $.ajax({
                    type: "POST",
                    url: 'delete_user.php',
                    data: { id: userIdToDelete },
                    success: function(response) {
                        $('#deleteUserModal').modal('hide'); // Hide the modal
                            location.reload(); // Reload the page
                        },
                        error: function(error) {
                            console.error("Erreur lors de la suppression de l'utilisateur:", error);
                        }
                    });
                }
            });

            // Optional: Clear user ID when modal is hidden
            $('#deleteUserModal').on('hidden.bs.modal', function() {
                userIdToDelete = null;
            });
        });
    </script>

    <!-- Delete User Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUser">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
