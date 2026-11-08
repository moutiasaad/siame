<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

$user = require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    db()->prepare('UPDATE alarms SET acknowledged_at = NOW(), acknowledged_by = ? WHERE id = ? AND acknowledged_at IS NULL')
        ->execute([$user['id'], (int) ($_POST['id'] ?? 0)]);
    flash('Alarme acquittée.');
    redirect('alarms.php');
}

$alarms = db()->query(
    "SELECT a.*, m.name AS machine, CONCAT(u.first_name, ' ', u.last_name) AS ack_by
     FROM alarms a LEFT JOIN machines m ON m.id = a.machine_id LEFT JOIN users u ON u.id = a.acknowledged_by
     ORDER BY a.acknowledged_at IS NOT NULL, a.created_at DESC LIMIT 100"
)->fetchAll();

page_start('Alarmes', 'alarms', $user, 'Intrusion (capteur PIR + SMS) et surchauffe (seuil de température)');
?>
<section class="card">
  <table class="table">
    <thead><tr><th>Type</th><th>Message</th><th>Machine</th><th>Date</th><th>État</th><th></th></tr></thead>
    <tbody>
    <?php if (!$alarms): ?><tr><td colspan="6" class="empty">Aucune alarme.</td></tr><?php endif; ?>
    <?php foreach ($alarms as $a): ?>
      <tr>
        <td><span class="pill <?= $a['type'] === 'intrusion' ? 'danger' : 'warn' ?>"><?= $a['type'] === 'intrusion' ? 'Intrusion' : 'Surchauffe' ?></span></td>
        <td class="cell-main"><?= e($a['message']) ?></td>
        <td><?= e($a['machine'] ?? 'Salle serveur') ?></td>
        <td><div><?= e(date('d/m/Y H:i', strtotime($a['created_at']))) ?></div><div class="cell-sub"><?= e(ago($a['created_at'])) ?></div></td>
        <td>
          <?php if ($a['acknowledged_at']): ?>
            <span class="pill ok">Acquittée</span><div class="cell-sub"><?= e($a['ack_by'] ?? '') ?></div>
          <?php else: ?>
            <span class="pill danger">Ouverte</span>
          <?php endif; ?>
        </td>
        <td class="text-right">
          <?php if (!$a['acknowledged_at']): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>"><button class="btn btn-ghost btn-sm"><i data-lucide="check"></i>Acquitter</button></form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php page_end(); ?>
