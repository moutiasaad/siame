// Pilote minimal du module GSM/GPRS SIM900 (commandes AT) : HTTP GET et SMS.
#pragma once
#include <Arduino.h>
#include <SoftwareSerial.h>

class Sim900 {
public:
  Sim900(uint8_t rx, uint8_t tx) : serial_(rx, tx) {}

  void begin(long baud);

  // Envoie une commande AT et attend `expect` dans la réponse (ou ERROR / délai dépassé).
  bool command(const char* cmd, const char* expect = "OK", unsigned long timeoutMs = 2000);

  // Ouvre le contexte GPRS (bearer) avec l'APN de l'opérateur.
  bool connectGprs(const char* apn);

  // Requête HTTP GET ; `body` reçoit le contenu de la réponse. Retourne le code HTTP (0 si échec).
  int httpGet(const String& url, String& body);

  bool sendSms(const char* number, const String& text);

  // Niveau du signal (0–31, 99 = inconnu).
  int signalQuality();

private:
  SoftwareSerial serial_;
  String last_;  // dernière réponse brute du module

  bool waitFor(const char* expect, unsigned long timeoutMs);
  void flushInput();
};
