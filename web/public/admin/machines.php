<?php
require __DIR__ . '/../../src/bootstrap.php';
require __DIR__ . '/../../src/layout.php';

$user = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        db()->prepare('DELETE FROM machines WHERE id = ?')->execute([(int) $_POST['id']]);
        flash('Machine supprimée.');
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $location = trim((string) ($_POST['location'] ?? ''));
        if ($name === '' || $location === '') {
            flash('Nom et emplacement sont obligatoires.', 'error');
        } else {
            db()->prepare('INSERT INTO machines (name, model, location, temp_max) VALUES (?, ?, ?, ?)')->execute([
                $name,
                trim((string) ($_POST['model'] ?? '')) ?: 'Billion Hercule H2000',
                $location,
                (float) ($_POST['temp_max'] ?? 60),
            ]);
            flash('Machine ajoutée. Indiquez son identifiant (MACHINE_ID) dans le firmware de la carte.');
        }
    }
    redirect('machines.php');
}

$machines = machines_with_status();
page_start('Machines', 'machines', $user, 'Chaque machine correspond à une carte Arduino + SIM900');
?>
<div class="grid layout-2">
  <section class="card">
    <table class="table">
      <thead><tr><th>ID</th><th>Machine</th><th>Liaison</th><th>Température</th><th>Seuil</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($machines as $m): ?>
        <tr>
          <td><span class="pill info" style="padding-left:8px"><?= (int) $m['id'] ?></span></td>
          <td><div class="cell-main"><?= e($m['name']) ?></div><div class="cell-sub"><?= e($m['model']) ?> · <?= e($m['location']) ?></div></td>
          <td><span class="pill <?= $m['online'] ? 'ok' : 'off' ?>"><?= $m['online'] ? 'En ligne' : 'Hors ligne' ?></span><div class="cell-sub"><?= e($m['last_sync_label']) ?></div></td>
          <td class="cell-main t-<?= $m['level'] ?>"><?= $m['temperature'] !== null ? number_format($m['temperature'], 1, ',', '') . ' °C' : '—' ?></td>
          <td><?= e(number_format((float) $m['temp_max'], 0)) ?> °C</td>
          <td class="text-right">
            <form method="post" onsubmit="return confirm('Supprimer cette machine et son historique ?')">
              <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>">
              <button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <div class="grid">
    <section class="card">
      <div class="card-head"><h2>Ajouter une machine</h2></div>
      <form class="card-body grid" method="post" style="gap:14px">
        <?= csrf_field() ?>
        <div class="field"><label>Nom</label><input name="name" placeholder="Presse H2000 — Ligne 4" required></div>
        <div class="field"><label>Modèle</label><input name="model" placeholder="Billion Hercule H2000"></div>
        <div class="field"><label>Emplacement</label><input name="location" placeholder="Atelier injection · Ligne 4" required></div>
        <div class="field"><label>Seuil d'alarme (°C)</label><input name="temp_max" type="number" step="0.5" value="60"></div>
        <button class="btn"><i data-lucide="plus"></i>Ajouter</button>
      </form>
    </section>
    <section class="card">
      <div class="card-head"><h2>Configurer la carte</h2></div>
      <div class="card-body">
        <p class="muted" style="margin-bottom:10px">La carte appelle cette adresse toutes les 30 s :</p>
        <div class="code">GET /api/device.php?key=DEVICE_KEY<br>&nbsp;&nbsp;&amp;machine=MACHINE_ID<br>&nbsp;&nbsp;&amp;temperature=46.2&amp;humidity=38</div>
        <p class="muted" style="margin-top:10px">Réponse : <strong>on</strong> ou <strong>off</strong>, appliqué sur la sortie relais.</p>
      </div>
    </section>
  </div>
</div>
<?php page_end(); ?>
