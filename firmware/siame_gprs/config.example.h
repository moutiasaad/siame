// Copier ce fichier en config.h et remplir les valeurs (config.h n'est pas versionné).
#pragma once

// --- Réseau mobile ---------------------------------------------------------
#define GPRS_APN          "weborange"          // APN de l'opérateur (Orange Tunisie)

// --- Serveur SIAME ---------------------------------------------------------
// Adresse de l'API appelée par la carte (HTTP simple : le SIM900 ne gère pas bien HTTPS)
#define API_URL           "http://exemple.siame.tn/api/device.php"
#define DEVICE_KEY        "change-me"           // même valeur que DEVICE_KEY côté serveur
#define MACHINE_ID        1                     // identifiant de la machine surveillée

// --- Alarme SMS ------------------------------------------------------------
#define ALARM_PHONE       "+216XXXXXXXX"        // numéro qui reçoit les alertes intrusion

// --- Rythme ----------------------------------------------------------------
#define TELEMETRY_PERIOD_MS   30000UL           // envoi température + lecture état : toutes les 30 s
#define ALARM_COOLDOWN_MS     60000UL           // 1 SMS maximum par minute
