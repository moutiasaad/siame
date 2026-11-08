#include <DHT.h>
#include"DHT.h"
#define DHTPIN A0
#define DHTTYPE DHT22
#include <SoftwareSerial.h>
SoftwareSerial SIM900(7,8);
DHT dht(DHTPIN,DHTTYPE);
int REDLed= 13;
float t;
String data;
void changeLed();
void etait_de_machine();
void envoyer_temperature(float t);
void setup() {     
  pinMode(REDLed, OUTPUT);
  Serial.begin(115200); 
  SIM900.begin(19200);
  void changeLed();
  Serial.println("DHT test!");
  dht.begin();
  
}
void loop()
{
  Serial.println("commencer " );
  etait_de_machine();
  t=dht.readTemperature();
  Serial.print(t);
  envoyer_temperature(t);
  Serial.println("termine " );
  delay(10000);
}
void etait_de_machine()
{
  SIM900.println("[SIM] Stopping");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("[SIM] Module disabled");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("[SIM] Starting");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("[SIM] Module started");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("AT");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+CSQ");
  delay(500);
  ShowSerialData();
  SIM900.println("AT+CGATT?");
  delay(100);
  delay(500);
  SIM900.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");
  delay(1000);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+SAPBR=3,1,\"APN\",\"weborange\"");
  delay(4000);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+SAPBR=\"1\",\"1\"");
  delay(2000);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+HTTPINIT");
  delay(2000); 
  ShowSerialData();
  delay(500);
  SIM900.println("AT+HTTPPARA=\"CID\",1");
  delay(2000); 
  ShowSerialData();
  delay(500);
  SIM900.println("AT+HTTPPARA=\"URL\",\"http://sim900asiame.tn/etaitmachine.php\"");
  delay(10000);
  ShowSerialData();
  SIM900.println("AT+HTTPACTION=0");
  delay(10000);
  ShowSerialData();
  SIM900.println("AT+HTTPREAD=0,100");
  delay(300);
  changeLed();
  ShowSerialData();
  Serial.println("");
  delay(100);
}
void envoyer_temperature( float t)
{
  SIM900.println("[SIM] Stopping");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("[SIM] Module disabled");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("[SIM] Starting");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("[SIM] Module started");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("AT");
  delay(500);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+CSQ");
  delay(500);
  ShowSerialData();
  SIM900.println("AT+CGATT?");
  delay(100);
  delay(500);
  SIM900.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");
  delay(1000);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+SAPBR=3,1,\"APN\",\"weborange\"");
  delay(4000);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+SAPBR=\"1\",\"1\"");
  delay(2000);
  ShowSerialData();
  delay(500);
  SIM900.println("AT+HTTPINIT");
  delay(2000); 
  ShowSerialData();
  delay(500);
  SIM900.println("AT+HTTPPARA=\"CID\",1");
  delay(2000); 
  ShowSerialData();
  delay(500);
  SIM900.print("AT+HTTPPARA=\"URL\",\"http://sim900asiame.tn/TestpSIM900.php?=\"");
  SIM900.println(t);
  delay(10000);
  ShowSerialData();
  SIM900.println("AT+HTTPACTION=0");
  delay(10000);
  ShowSerialData();
}
void changeLed()
{
  while(SIM900.available()!=0)
{
 data = data + String(char (SIM900.read()));
}
    Serial.write(char (SIM900.read()));
 if(data.substring(0,2)== "on")
 {
   digitalWrite(REDLed, HIGH);
 }
 else if (data.substring(0,3)== "off")
 {
   digitalWrite(REDLed, LOW);
 }
 data = "";
}
void ShowSerialData()
{
  while(SIM900.available()!=0)
    Serial.write(char (SIM900.read()));
}
