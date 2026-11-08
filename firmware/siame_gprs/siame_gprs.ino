// SIAME — supervision d'une machine à distance par GPRS (Arduino Uno + SIM900)
//
//  * toutes les 30 s : mesure température / humidité (DHT22), envoi au serveur,
//    et application de l'état demandé depuis le site (marche / arrêt) sur la sortie relais ;
//  * capteur de mouvement PIR (salle serveur) : SMS d'alerte + buzzer + alarme enregistrée sur le site.
//
// Câblage : SIM900 RX/TX -> D7/D8 · DHT22 -> A0 · PIR -> D3 · relais/LED machine -> D13 · buzzer -> D2

#include <DHT.h>
#include "config.h"
#include "sim900.h"

const uint8_t PIN_DHT = A0;
const uint8_t PIN_PIR = 3;
const uint8_t PIN_BUZZER = 2;
const uint8_t PIN_MACHINE = 13;

DHT dht(PIN_DHT, DHT22);
Sim900 gsm(7, 8);

unsigned long lastTelemetry = 0;
unsigned long lastAlarm = 0;
unsigned long buzzerUntil = 0;
bool gprsReady = false;
bool pirWasActive = false;

String apiUrl(const String& query) {
  return String(API_URL) + "?key=" + DEVICE_KEY + "&machine=" + MACHINE_ID + query;
}

void applyMachineState(const String& state) {
  if (state == "on") digitalWrite(PIN_MACHINE, HIGH);
  else if (state == "off") digitalWrite(PIN_MACHINE, LOW);
}

void sendTelemetry() {
  const float t = dht.readTemperature();
  const float h = dht.readHumidity();
  if (isnan(t) || isnan(h)) {
    Serial.println(F("[DHT22] lecture impossible"));
    return;
  }
  if (!gprsReady) gprsReady = gsm.connectGprs(GPRS_APN);
  if (!gprsReady) {
    Serial.println(F("[GPRS] pas de connexion, nouvel essai au prochain cycle"));
    return;
  }

  // Une seule requête : envoie la mesure et reçoit l'état demandé ("on" / "off")
  String state;
  const int code = gsm.httpGet(apiUrl("&temperature=" + String(t, 1) + "&humidity=" + String(h, 1)), state);
  Serial.print(F("[HTTP] ")); Serial.print(code);
  Serial.print(F(" · T=")); Serial.print(t, 1);
  Serial.print(F(" °C · etat=")); Serial.println(state);

  if (code == 200) applyMachineState(state);
  else gprsReady = false;  // on reconnecte au prochain cycle
}

void raiseIntrusionAlarm() {
  Serial.println(F("[ALARME] mouvement détecté dans la salle serveur"));
  digitalWrite(PIN_BUZZER, HIGH);
  buzzerUntil = millis() + 5000;
  gsm.sendSms(ALARM_PHONE, F("SIAME : mouvement detecte dans la salle serveur. Verifiez l'acces."));
  String ignored;
  if (gprsReady) gsm.httpGet(apiUrl("&event=intrusion"), ignored);
}

void setup() {
  Serial.begin(115200);
  pinMode(PIN_PIR, INPUT);
  pinMode(PIN_BUZZER, OUTPUT);
  pinMode(PIN_MACHINE, OUTPUT);
  dht.begin();
  gsm.begin(19200);
  Serial.print(F("[SIM900] signal : ")); Serial.println(gsm.signalQuality());
  gprsReady = gsm.connectGprs(GPRS_APN);
  lastTelemetry = millis() - TELEMETRY_PERIOD_MS;  // premier envoi immédiat
}

void loop() {
  const unsigned long now = millis();

  // Front montant du PIR + délai anti-spam entre deux SMS
  const bool pirActive = digitalRead(PIN_PIR) == HIGH;
  if (pirActive && !pirWasActive && now - lastAlarm > ALARM_COOLDOWN_MS) {
    lastAlarm = now;
    raiseIntrusionAlarm();
  }
  pirWasActive = pirActive;

  if (buzzerUntil && now > buzzerUntil) {
    digitalWrite(PIN_BUZZER, LOW);
    buzzerUntil = 0;
  }

  if (now - lastTelemetry >= TELEMETRY_PERIOD_MS) {
    lastTelemetry = now;
    sendTelemetry();
  }
}
