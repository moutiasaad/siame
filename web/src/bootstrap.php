<?php
// Chargé en premier par chaque page : configuration, base de données, session, fonctions utiles.
declare(strict_types=1);

date_default_timezone_set('Africa/Tunis');

$configFile = __DIR__ . '/../config.php';
$config = require (is_file($configFile) ? $configFile : __DIR__ . '/../config.example.php');

function db(): PDO
{
    static $pdo = null;
    global $config;
    if ($pdo === null) {
        $pdo = new PDO($config['db']['dsn'], $config['db']['user'], $config['db']['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function config(string $key): mixed
{
    global $config;
    return $config[$key] ?? null;
}

function session_start_once(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

/** Échappement HTML. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

// --- Authentification -------------------------------------------------------

function current_user(): ?array
{
    session_start_once();
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        redirect(base_url('index.php'));
    }
    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        exit('Accès réservé aux administrateurs.');
    }
    return $user;
}

/** URL relative à la racine publique (fonctionne aussi dans /admin et /api). */
function base_url(string $path = ''): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $depth = preg_match('#/(admin|api)/[^/]+$#', $script) ? '../' : '';
    return $depth . $path;
}

// --- Protection CSRF des formulaires ---------------------------------------

function csrf_token(): string
{
    session_start_once();
    $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function check_csrf(): void
{
    $token = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals(csrf_token(), (string) $token)) {
        http_response_code(419);
        exit('Session expirée, rechargez la page.');
    }
}

// --- Messages flash ---------------------------------------------------------

function flash(string $message, string $type = 'success'): void
{
    session_start_once();
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function take_flash(): ?array
{
    session_start_once();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// --- Machines ---------------------------------------------------------------

function is_online(?string $lastSync): bool
{
    return $lastSync !== null && (time() - strtotime($lastSync)) <= (int) config('offline_after');
}

/** « il y a 2 min » */
function ago(?string $datetime): string
{
    if (!$datetime) {
        return 'jamais';
    }
    $s = max(0, time() - strtotime($datetime));
    return match (true) {
        $s < 60 => "il y a {$s} s",
        $s < 3600 => 'il y a ' . intdiv($s, 60) . ' min',
        $s < 86400 => 'il y a ' . intdiv($s, 3600) . ' h',
        default => 'le ' . date('d/m/Y', strtotime($datetime)),
    };
}

/** Niveau de température : ok / warn (≥ 85 % du seuil) / danger (≥ seuil). */
function temp_level(?float $temperature, float $max): string
{
    if ($temperature === null) {
        return 'muted';
    }
    return $temperature >= $max ? 'danger' : ($temperature >= 0.85 * $max ? 'warn' : 'ok');
}

function machines_with_status(): array
{
    $machines = db()->query('SELECT * FROM machines ORDER BY id')->fetchAll();
    foreach ($machines as &$m) {
        $m['online'] = is_online($m['last_sync']);
        $m['temperature'] = $m['temperature'] !== null ? (float) $m['temperature'] : null;
        $m['humidity'] = $m['humidity'] !== null ? (float) $m['humidity'] : null;
        $m['level'] = temp_level($m['temperature'], (float) $m['temp_max']);
        $m['last_sync_label'] = ago($m['last_sync']);
    }
    return $machines;
}

function open_alarms_count(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM alarms WHERE acknowledged_at IS NULL')->fetchColumn();
}
