<?php
// Marche / arrêt demandé depuis le site (utilisateur connecté + jeton CSRF).
// La carte applique le nouvel état à sa prochaine requête GPRS (≤ 30 s).
require __DIR__ . '/../../src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$user = current_user();
if (!$user) {
    http_response_code(401);
    exit(json_encode(['error' => 'Non connecté']));
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'POST attendu']));
}
check_csrf();

$id = (int) ($_POST['id'] ?? 0);
$state = $_POST['state'] ?? '';
if (!in_array($state, ['on', 'off'], true)) {
    http_response_code(422);
    exit(json_encode(['error' => 'État invalide']));
}

$stmt = db()->prepare('UPDATE machines SET state = ? WHERE id = ?');
$stmt->execute([$state, $id]);
if ($stmt->rowCount() > 0) {
    db()->prepare('INSERT INTO commands (machine_id, user_id, state) VALUES (?, ?, ?)')->execute([$id, $user['id'], $state]);
}
echo json_encode(['id' => $id, 'state' => $state]);
