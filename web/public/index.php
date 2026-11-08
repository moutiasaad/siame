<?php
// Connexion (mots de passe hachés avec password_hash)
require __DIR__ . '/../src/bootstrap.php';

if (current_user()) {
    redirect('dashboard.php');
}

$error = null;
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify((string) ($_POST['password'] ?? ''), $user['password_hash'])) {
        session_regenerate_id(true);
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        redirect('dashboard.php');
    }
    $error = 'Email ou mot de passe incorrect.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion · SIAME Supervision</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
  <script src="https://cdn.jsdelivr.net/npm/lucide@0.460.0/dist/umd/lucide.min.js"></script>
</head>
<body>
<div class="login">
  <section class="login-visual">
    <span class="pill info" style="width:max-content;margin-bottom:16px">Atelier injection · Grombalia</span>
    <h2>Supervisez vos machines à distance, où que vous soyez.</h2>
    <p>Chaque presse est équipée d'une carte Arduino et d'un module GSM/GPRS SIM900 : température en direct,
       marche / arrêt à distance et alerte SMS en cas d'intrusion dans la salle serveur.</p>
    <div class="features">
      <span><i data-lucide="thermometer"></i>Température en direct</span>
      <span><i data-lucide="power"></i>Marche / arrêt à distance</span>
      <span><i data-lucide="message-square-warning"></i>Alertes SMS</span>
    </div>
  </section>
  <section class="login-form">
    <form method="post" autocomplete="on">
      <img class="logo" src="assets/img/siame.png" alt="SIAME">
      <div>
        <h1>Espace employés</h1>
        <p class="muted">Connectez-vous pour accéder à la supervision.</p>
      </div>
      <?php if ($error): ?><div class="flash flash-error" style="margin:0"><?= e($error) ?></div><?php endif; ?>
      <?= csrf_field() ?>
      <div class="field">
        <label for="email">Email professionnel</label>
        <input id="email" name="email" type="email" value="<?= e($email) ?>" placeholder="prenom.nom@siame.tn" required>
      </div>
      <div class="field">
        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" placeholder="••••••••" required>
      </div>
      <button class="btn" style="justify-content:center;padding:13px">Se connecter <i data-lucide="arrow-right"></i></button>
      <p class="muted" style="font-size:12.5px;text-align:center">Accès réservé au personnel SIAME · compte créé par l'administrateur</p>
    </form>
  </section>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
