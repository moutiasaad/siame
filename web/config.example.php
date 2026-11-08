<?php
// Copier en config.php (non versionné) et adapter.
return [
    'db' => [
        'dsn'      => 'mysql:host=127.0.0.1;dbname=siame;charset=utf8mb4',
        'user'     => 'root',
        'password' => '',
    ],
    // Clé partagée avec le firmware (DEVICE_KEY dans firmware/siame_gprs/config.h)
    'device_key' => 'change-me',
    // Une machine est « hors ligne » si elle n'a rien envoyé depuis ce délai (secondes)
    'offline_after' => 120,
];
