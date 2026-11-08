# Code d'origine — stage SIAME (janvier–février 2022)

Code tel qu'il a été écrit pendant le stage de 2e année, conservé pour l'historique.
La version actuelle (firmware, application web, simulateur) se trouve à la racine du dépôt.

- `firmware/temperature_gprs_etat_machine.ino` : envoi de la température (DHT22) par GPRS et lecture de l'état on/off de la machine
- `firmware/alarme_sms_pir.ino` : alarme SMS déclenchée par le capteur de mouvement PIR (salle serveur)
- `firmware/*` : essais SIM900 (connexion GPRS, requêtes HTTP, alimentation)
- `web/` : première page de contrôle (PHP + MySQL)
