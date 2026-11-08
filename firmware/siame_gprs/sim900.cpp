#include "sim900.h"

void Sim900::begin(long baud) {
  serial_.begin(baud);
  delay(1000);
  command("AT");
  command("ATE0");          // pas d'écho : réponses plus simples à lire
  command("AT+CMGF=1");     // SMS en mode texte
}

void Sim900::flushInput() {
  while (serial_.available()) serial_.read();
}

bool Sim900::waitFor(const char* expect, unsigned long timeoutMs) {
  last_ = "";
  const unsigned long start = millis();
  while (millis() - start < timeoutMs) {
    while (serial_.available()) {
      last_ += (char)serial_.read();
      if (last_.indexOf(expect) >= 0) return true;
      if (last_.indexOf("ERROR") >= 0) return false;
    }
  }
  return false;
}

bool Sim900::command(const char* cmd, const char* expect, unsigned long timeoutMs) {
  flushInput();
  serial_.println(cmd);
  return waitFor(expect, timeoutMs);
}

bool Sim900::connectGprs(const char* apn) {
  command("AT+SAPBR=0,1", "OK", 3000);  // ferme un ancien contexte s'il existe (erreur ignorée)
  if (!command("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"")) return false;
  String apnCmd = String("AT+SAPBR=3,1,\"APN\",\"") + apn + "\"";
  if (!command(apnCmd.c_str())) return false;
  return command("AT+SAPBR=1,1", "OK", 15000);
}

int Sim900::httpGet(const String& url, String& body) {
  body = "";
  command("AT+HTTPTERM", "OK", 1000);  // repart d'une session HTTP propre
  if (!command("AT+HTTPINIT")) return 0;
  command("AT+HTTPPARA=\"CID\",1");
  String urlCmd = String("AT+HTTPPARA=\"URL\",\"") + url + "\"";
  if (!command(urlCmd.c_str())) return 0;

  // Réponse attendue : +HTTPACTION: 0,<code>,<longueur>
  if (!command("AT+HTTPACTION=0", "+HTTPACTION:", 20000)) return 0;
  waitFor("\n", 2000);
  const int c1 = last_.indexOf(',');
  const int status = (c1 >= 0) ? last_.substring(c1 + 1).toInt() : 0;

  // +HTTPREAD: <longueur>\r\n<contenu>\r\nOK
  if (command("AT+HTTPREAD", "OK", 5000)) {
    const int start = last_.indexOf('\n', last_.indexOf("+HTTPREAD:"));
    const int end = last_.lastIndexOf("OK");
    if (start >= 0 && end > start) {
      body = last_.substring(start + 1, end);
      body.trim();
    }
  }
  command("AT+HTTPTERM");
  return status;
}

bool Sim900::sendSms(const char* number, const String& text) {
  String cmd = String("AT+CMGS=\"") + number + "\"";
  if (!command(cmd.c_str(), ">", 5000)) return false;
  serial_.print(text);
  serial_.write(26);  // Ctrl+Z : envoi du message
  return waitFor("OK", 20000);
}

int Sim900::signalQuality() {
  if (!command("AT+CSQ", "OK", 2000)) return 99;
  const int p = last_.indexOf("+CSQ:");
  return p >= 0 ? last_.substring(p + 5).toInt() : 99;
}
