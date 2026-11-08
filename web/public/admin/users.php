<?php
require __DIR__ . '/../../src/bootstrap.php';
require __DIR__ . '/../../src/layout.php';

$user = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int) $_POST['id'];
        if ($id === (int) $user['id']) {
            flash('Vous ne pouvez pas supprimer votre propre compte.', 'error');
        } else {
            db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            flash('Utilisateur supprimé.');
        }
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $exists = db()->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $exists->execute([$email]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            flash('Email valide et mot de passe de 8 caractères minimum requis.', 'error');
        } elseif ((int) $exists->fetchColumn() > 0) {
            flash('Cet email est déjà utilisé.', 'error');
        } else {
            db()->prepare('INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)')->execute([
                trim((string) $_POST['first_name']),
                trim((string) $_POST['last_name']),
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'operator',
            ]);
            flash('Utilisateur créé.');
        }
    }
    redirect('users.php');
}

$users = db()->query('SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY role, last_name')->fetchAll();
page_start('Utilisateurs', 'users', $user, 'Comptes ayant accès à la supervision');
?>
<div class="grid layout-2">
  <section class="card">
    <table class="table">
      <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Créé le</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><div style="display:flex;align-items:center;gap:10px"><span class="avatar" style="width:34px;height:34px;font-size:13px"><?= e(mb_strtoupper(mb_substr($u['first_name'], 0, 1) . mb_substr($u['last_name'], 0, 1))) ?></span><span class="cell-main"><?= e($u['first_name'] . ' ' . $u['last_name']) ?></span></div></td>
          <td><?= e($u['email']) ?></td>
          <td><span class="pill <?= $u['role'] === 'admin' ? 'info' : 'off' ?>"><?= $u['role'] === 'admin' ? 'Administrateur' : 'Opérateur' ?></span></td>
          <td class="cell-sub"><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td>
          <td class="text-right">
            <?php if ((int) $u['id'] !== (int) $user['id']): ?>
              <form method="post" onsubmit="return confirm('Supprimer ce compte ?')">
                <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>
  <section class="card">
    <div class="card-head"><h2>Nouvel utilisateur</h2></div>
    <form class="card-body grid" method="post" style="gap:14px">
      <?= csrf_field() ?>
      <div class="form-grid">
        <div class="field"><label>Prénom</label><input name="first_name" required></div>
        <div class="field"><label>Nom</label><input name="last_name" required></div>
      </div>
      <div class="field"><label>Email</label><input name="email" type="email" required></div>
      <div class="field"><label>Mot de passe</label><input name="password" type="password" minlength="8" required></div>
      <div class="field"><label>Rôle</label><select name="role"><option value="operator">Opérateur</option><option value="admin">Administrateur</option></select></div>
      <button class="btn"><i data-lucide="user-plus"></i>Créer le compte</button>
    </form>
  </section>
</div>
<?php page_end(); ?>
