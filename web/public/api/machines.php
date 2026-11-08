<?php
// État de toutes les machines (rafraîchissement automatique du tableau de bord).
require __DIR__ . '/../../src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
if (!current_user()) {
    http_response_code(401);
    exit(json_encode(['error' => 'Non connecté']));
}

$out = [];
foreach (machines_with_status() as $m) {
    $out[] = [
        'id' => (int) $m['id'],
        'state' => $m['state'],
        'online' => $m['online'],
        'temperature' => $m['temperature'],
        'humidity' => $m['humidity'],
        'level' => $m['level'],
        'sync' => $m['last_sync_label'],
    ];
}
echo json_encode($out);
