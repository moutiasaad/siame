<?php
// Point d'entrée appelé par la carte Arduino + SIM900 (requête HTTP GET, réponse en texte brut).
//
//   GET api/device.php?key=…&machine=1&temperature=72.4&humidity=41.0   → enregistre la mesure
//   GET api/device.php?key=…&machine=1&event=intrusion                  → enregistre une alarme intrusion
//
// Réponse : « on » ou « off » = état demandé depuis le site, appliqué par la carte sur la sortie relais.
require __DIR__ . '/../../src/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

if (!hash_equals((string) config('device_key'), (string) ($_GET['key'] ?? ''))) {
    http_response_code(401);
    exit('unauthorized');
}

$machineId = (int) ($_GET['machine'] ?? 0);
$stmt = db()->prepare('SELECT * FROM machines WHERE id = ?');
$stmt->execute([$machineId]);
$machine = $stmt->fetch();
if (!$machine) {
    http_response_code(404);
    exit('unknown machine');
}

$pdo = db();

if (isset($_GET['temperature'])) {
    $t = round((float) $_GET['temperature'], 1);
    $h = isset($_GET['humidity']) ? round((float) $_GET['humidity'], 1) : null;
    if ($t < -40 || $t > 80) { // plage de mesure du DHT22 : -40 à +80 °C
        http_response_code(422);
        exit('invalid temperature');
    }
    $pdo->prepare('INSERT INTO measurements (machine_id, temperature, humidity) VALUES (?, ?, ?)')
        ->execute([$machineId, $t, $h]);
    $pdo->prepare('UPDATE machines SET temperature = ?, humidity = ?, last_sync = NOW() WHERE id = ?')
        ->execute([$t, $h, $machineId]);

    // Alarme surchauffe : une seule alarme ouverte à la fois par machine
    if ($t >= (float) $machine['temp_max']) {
        $open = $pdo->prepare("SELECT COUNT(*) FROM alarms WHERE machine_id = ? AND type = 'temperature' AND acknowledged_at IS NULL");
        $open->execute([$machineId]);
        if ((int) $open->fetchColumn() === 0) {
            $pdo->prepare("INSERT INTO alarms (machine_id, type, message) VALUES (?, 'temperature', ?)")
                ->execute([$machineId, sprintf('Surchauffe : %.1f °C (seuil %.0f °C)', $t, $machine['temp_max'])]);
        }
    }
} else {
    $pdo->prepare('UPDATE machines SET last_sync = NOW() WHERE id = ?')->execute([$machineId]);
}

if (($_GET['event'] ?? '') === 'intrusion') {
    $pdo->prepare("INSERT INTO alarms (machine_id, type, message) VALUES (?, 'intrusion', ?)")
        ->execute([$machineId, 'Mouvement détecté dans la salle serveur — SMS envoyé']);
}

echo $machine['state']; // « on » / « off »
