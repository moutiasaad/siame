<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

$user = require_login();
$id = (int) ($_GET['id'] ?? 0);
$found = array_filter(machines_with_status(), fn ($m) => (int) $m['id'] === $id);
$machine = $found ? reset($found) : null;
if (!$machine) {
    http_response_code(404);
    exit('Machine introuvable.');
}

$stats = db()->prepare(
    'SELECT MIN(temperature) AS tmin, MAX(temperature) AS tmax, AVG(temperature) AS tavg, COUNT(*) AS n
     FROM measurements WHERE machine_id = ? AND created_at >= NOW() - INTERVAL 24 HOUR'
);
$stats->execute([$id]);
$stats = $stats->fetch();

$commands = db()->prepare(
    "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) AS who FROM commands c
     LEFT JOIN users u ON u.id = c.user_id WHERE c.machine_id = ? ORDER BY c.created_at DESC LIMIT 8"
);
$commands->execute([$id]);
$commands = $commands->fetchAll();

$fmt = fn ($v) => $v !== null ? number_format((float) $v, 1, ',', '') . ' °C' : '—';
page_start($machine['name'], 'dashboard', $user, $machine['model'] . ' · ' . $machine['location']);
?>
<section class="card detail-hero" data-machine="<?= $machine['id'] ?>">
  <div class="visual"><img src="assets/img/machine.png" alt="<?= e($machine['model']) ?>"></div>
  <div class="hero-info">
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <span class="pill <?= $machine['online'] ? 'ok' : 'off' ?>" data-field="online"><?= $machine['online'] ? 'En ligne' : 'Hors ligne' ?></span>
      <span class="pill info">Seuil d'alarme <?= e(number_format((float) $machine['temp_max'], 0)) ?> °C</span>
    </div>
    <div class="readings">
      <div class="reading"><div class="r-label">Température actuelle</div><div class="r-value t-<?= $machine['level'] ?>" data-field="temperature"><?= $machine['temperature'] !== null ? number_format($machine['temperature'], 1, ',', '') : '—' ?><small> °C</small></div></div>
      <div class="reading"><div class="r-label">Humidité</div><div class="r-value" data-field="humidity"><?= $machine['humidity'] !== null ? number_format($machine['humidity'], 0) : '—' ?><small> %</small></div></div>
    </div>
    <div class="stats">
      <div class="reading"><div class="r-label">Min 24 h</div><strong><?= $fmt($stats['tmin']) ?></strong></div>
      <div class="reading"><div class="r-label">Moyenne 24 h</div><strong><?= $fmt($stats['tavg']) ?></strong></div>
      <div class="reading"><div class="r-label">Max 24 h</div><strong><?= $fmt($stats['tmax']) ?></strong></div>
    </div>
    <div class="machine-foot">
      <span class="sync"><i data-lucide="refresh-cw"></i>Dernier message GPRS <span data-field="sync"><?= e($machine['last_sync_label']) ?></span></span>
      <label class="switch">
        <span data-field="state-label"><?= $machine['state'] === 'on' ? 'Marche' : 'Arrêt' ?></span>
        <input type="checkbox" data-toggle="<?= $machine['id'] ?>" <?= $machine['state'] === 'on' ? 'checked' : '' ?>>
        <span class="track"></span>
      </label>
    </div>
  </div>
</section>

<div class="grid layout-2">
  <section class="card">
    <div class="card-head"><h2>Température · dernières 24 h</h2><span class="muted"><?= (int) $stats['n'] ?> mesures</span></div>
    <div class="card-body"><div class="chart-box"><canvas data-history="<?= $machine['id'] ?>" data-max="<?= e($machine['temp_max']) ?>"></canvas></div></div>
  </section>
  <section class="card">
    <div class="card-head"><h2>Commandes marche / arrêt</h2></div>
    <?php if (!$commands): ?>
      <div class="empty">Aucune commande pour l'instant.</div>
    <?php else: ?>
      <ul class="alarm-list">
        <?php foreach ($commands as $c): ?>
          <li class="alarm-item">
            <span class="alarm-icon" style="background:<?= $c['state'] === 'on' ? '#dcfce7;color:#15803d' : '#f1f5f9;color:#475569' ?>"><i data-lucide="power"></i></span>
            <div><strong><?= $c['state'] === 'on' ? 'Mise en marche' : 'Arrêt' ?></strong><span><?= e($c['who'] ?? '—') ?> · <?= e(date('d/m/Y H:i', strtotime($c['created_at']))) ?></span></div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</div>
<?php page_end(); ?>
