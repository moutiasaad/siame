<?php
// En-tête (barre latérale + barre du haut) et pied de page communs aux pages connectées.

function page_start(string $title, string $active, array $user, string $subtitle = ''): void
{
    $nav = [
        ['dashboard', 'dashboard.php', 'layout-dashboard', 'Tableau de bord', false],
        ['alarms', 'alarms.php', 'siren', 'Alarmes', false],
        ['machines', 'admin/machines.php', 'factory', 'Machines', true],
        ['users', 'admin/users.php', 'users', 'Utilisateurs', true],
    ];
    $openAlarms = open_alarms_count();
    $initials = mb_strtoupper(mb_substr($user['first_name'], 0, 1) . mb_substr($user['last_name'], 0, 1));
    $flash = take_flash();
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf" content="<?= e(csrf_token()) ?>">
  <title><?= e($title) ?> · SIAME Supervision</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
  <script src="https://cdn.jsdelivr.net/npm/lucide@0.460.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <a class="brand" href="<?= base_url('dashboard.php') ?>">
      <span class="brand-logo"><img src="<?= base_url('assets/img/siame.png') ?>" alt="SIAME"></span>
      <span class="brand-sub">Supervision GPRS</span>
    </a>
    <nav>
      <p class="nav-label">Supervision</p>
      <?php foreach ($nav as [$key, $href, $icon, $label, $adminOnly]): ?>
        <?php if ($adminOnly && $user['role'] !== 'admin') continue; ?>
        <?php if ($key === 'machines'): ?><p class="nav-label">Administration</p><?php endif; ?>
        <a class="nav-item <?= $active === $key ? 'active' : '' ?>" href="<?= base_url($href) ?>">
          <i data-lucide="<?= $icon ?>"></i><span><?= $label ?></span>
          <?php if ($key === 'alarms' && $openAlarms > 0): ?><span class="nav-badge"><?= $openAlarms ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
      <div class="gprs"><i data-lucide="radio-tower"></i><div><strong>Liaison GPRS</strong><span>SIM900 · Orange TN</span></div></div>
      <a class="nav-item" href="<?= base_url('logout.php') ?>"><i data-lucide="log-out"></i><span>Déconnexion</span></a>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div>
        <h1><?= e($title) ?></h1>
        <?php if ($subtitle): ?><p class="muted"><?= e($subtitle) ?></p><?php endif; ?>
      </div>
      <div class="user-chip">
        <div class="text-right">
          <strong><?= e($user['first_name'] . ' ' . $user['last_name']) ?></strong>
          <span><?= $user['role'] === 'admin' ? 'Administrateur' : 'Opérateur' ?></span>
        </div>
        <span class="avatar"><?= e($initials) ?></span>
      </div>
    </header>
    <main class="content">
      <?php if ($flash): ?><div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<?php
}

function page_end(): void
{
    ?>
    </main>
  </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
<?php
}
