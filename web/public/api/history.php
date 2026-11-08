<?php
// Historique de température d'une machine (graphiques). ?id=1&hours=24
require __DIR__ . '/../../src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
if (!current_user()) {
    http_response_code(401);
    exit(json_encode(['error' => 'Non connecté']));
}

$id = (int) ($_GET['id'] ?? 0);
$hours = max(1, min(168, (int) ($_GET['hours'] ?? 24)));

// Moyenne par tranche de 10 minutes : courbe lisible même avec une mesure toutes les 30 s
$stmt = db()->prepare(
    "SELECT DATE_FORMAT(MIN(created_at), '%Y-%m-%dT%H:%i') AS t, ROUND(AVG(temperature), 1) AS v
     FROM measurements
     WHERE machine_id = ? AND created_at >= NOW() - INTERVAL ? HOUR
     GROUP BY FLOOR(UNIX_TIMESTAMP(created_at) / 600)
     ORDER BY t"
);
$stmt->execute([$id, $hours]);
echo json_encode($stmt->fetchAll());
