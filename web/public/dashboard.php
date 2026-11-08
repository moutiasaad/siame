<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

$user = require_login();
$machines = machines_with_status();
$alarms = db()->query(
    'SELECT a.*, m.name AS machine FROM alarms a LEFT JOIN machines m ON m.id = a.machine_id
     ORDER BY a.created_at DESC LIMIT 6'
)->fetchAll();

$total = count($machines);
$running = count(array_filter($machines, fn ($m) => $m['state'] === 'on'));
$online = count(array_filter($machines, fn ($m) => $m['online']));
$temps = array_filter(array_column($machines, 'temperature'), fn ($t) => $t !== null);
$avgTemp = $temps ? array_sum($temps) / count($temps) : null;
$openAlarms = open_alarms_count();

page_start('Tableau de bord', 'dashboard', $user, 'Atelier injection · ' . date('d/m/Y'));
?>
<div class="grid kpis">
  <div class="card kpi"><div><div class="label">Machines en marche</div><div class="value" data-kpi="running"><?= $running ?><small> / <?= $total ?></small></div><div class="hint">Commandées depuis le site</div></div><span class="kpi-icon bg-ok"><i data-lucide="power"></i></span></div>
  <div class="card kpi"><div><div class="label">Cartes en ligne</div><div class="value" data-kpi="online"><?= $online ?><small> / <?= $total ?></small></div><div class="hint">Message GPRS il y a moins de 2 min</div></div><span class="kpi-icon bg-cyan"><i data-lucide="radio-tower"></i></span></div>
  <div class="card kpi"><div><div class="label">Température moyenne</div><div class="value" data-kpi="avg"><?= $avgTemp !== null ? number_format($avgTemp, 1, ',', ' ') : '—' ?><small> °C</small></div><div class="hint">Capteurs DHT22</div></div><span class="kpi-icon bg-blue"><i data-lucide="thermometer"></i></span></div>
  <div class="card kpi"><div><div class="label">Alarmes ouvertes</div><div class="value"><?= $openAlarms ?></div><div class="hint">Intrusion ou surchauffe</div></div><span class="kpi-icon <?= $openAlarms ? 'bg-danger' : 'bg-ok' ?>"><i data-lucide="siren"></i></span></div>
</div>

<div class="grid layout-2">
  <div class="grid machines machines-2">
    <?php foreach ($machines as $m): ?>
      <article class="card machine" data-machine="<?= $m['id'] ?>">
        <div class="machine-top">
          <span class="pill <?= $m['online'] ? 'ok' : 'off' ?>" data-field="online"><?= $m['online'] ? 'En ligne' : 'Hors ligne' ?></span>
          <img src="assets/img/machine.png" alt="">
        </div>
        <div class="machine-body">
          <div>
            <a class="machine-name" href="machine.php?id=<?= $m['id'] ?>"><?= e($m['name']) ?></a>
            <div class="machine-loc"><i data-lucide="map-pin"></i><?= e($m['location']) ?></div>
          </div>
          <div class="readings">
            <div class="reading">
              <div class="r-label">Température</div>
              <div class="r-value t-<?= $m['level'] ?>" data-field="temperature"><?= $m['temperature'] !== null ? number_format($m['temperature'], 1, ',', '') : '—' ?><small> °C</small></div>
            </div>
            <div class="reading">
              <div class="r-label">Humidité</div>
              <div class="r-value" data-field="humidity"><?= $m['humidity'] !== null ? number_format($m['humidity'], 0) : '—' ?><small> %</small></div>
            </div>
          </div>
          <div class="spark"><canvas data-spark="<?= $m['id'] ?>" data-max="<?= e($m['temp_max']) ?>"></canvas></div>
          <div class="machine-foot">
            <span class="sync"><i data-lucide="refresh-cw"></i><span data-field="sync"><?= e($m['last_sync_label']) ?></span></span>
            <label class="switch">
              <span data-field="state-label"><?= $m['state'] === 'on' ? 'Marche' : 'Arrêt' ?></span>
              <input type="checkbox" data-toggle="<?= $m['id'] ?>" <?= $m['state'] === 'on' ? 'checked' : '' ?>>
              <span class="track"></span>
            </label>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <section class="card">
    <div class="card-head"><h2>Dernières alarmes</h2><a class="btn btn-ghost btn-sm" href="alarms.php">Tout voir</a></div>
    <?php if (!$alarms): ?>
      <div class="empty">Aucune alarme enregistrée.</div>
    <?php else: ?>
      <ul class="alarm-list">
        <?php foreach ($alarms as $a): ?>
          <li class="alarm-item">
            <span class="alarm-icon <?= e($a['type']) ?>"><i data-lucide="<?= $a['type'] === 'intrusion' ? 'shield-alert' : 'thermometer-sun' ?>"></i></span>
            <div style="flex:1">
              <strong><?= e($a['message']) ?></strong>
              <span><?= e($a['machine'] ?? 'Salle serveur') ?> · <?= e(ago($a['created_at'])) ?></span>
            </div>
            <?php if (!$a['acknowledged_at']): ?><span class="pill danger">Ouverte</span><?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</div>
<?php page_end(); ?>
