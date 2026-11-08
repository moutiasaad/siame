
#include <SoftwareSerial.h>
SoftwareSerial SIM900(7, 8);
int REDLed= 2;
void setup() {     
  pinMode(REDLed, OUTPUT);
  SIM900.begin(19200);
  Serial.begin(19200); 
  delay(30000); 
}
void loop()
{
  Serial.println("commencer " );
  SubmitHttpRequest();
  Serial.println("termine " );
  delay(10000);
}
void SubmitHttpRequest()
{
  SIM900.println("AT+SAPBR=3,1,\"APN\",\"Ooredoo\"");
  delay(100);
  ShowSerialData();
  SIM900.println("AT+SAPBR=1,1");
  delay(2000);
  ShowSerialData();
  SIM900.println("AT+HTTPINIT");
  delay(2000); 
  ShowSerialData();
  SIM900.println("\"CID\",1");
  delay(2000); 
  ShowSerialData();
  SIM900.println("AT+HTTPPARA=\"URL\",\"http://sim900asiame.000webhostapp.com/TestSIM900.php\"");
  delay(1000);
  ShowSerialData();
  SIM900.println("AT+HTTPACTION=0");
  delay(10000);
  ShowSerialData();
  SIM900.println("AT+HTTPREAD=4");
  delay(300);
  changeLed();
  ShowSerialData();
  SIM900.println("");
  delay(100);
}
void changeLed()
{
 String content = "";
 while(SIM900.available()!=0)
 {  
    content = content + String(char (SIM900.read()));
 }
    Serial.println(content);
 if(content.substring(0,2)== "ON")
 {
   digitalWrite(REDLed, HIGH);
 }
 else if (content.substring(0,3)== "OFF")
 {
   digitalWrite(REDLed, LOW);
 }
 content = "";
}
void ShowSerialData()
{
  while(SIM900.available()!=0)
    Serial.write(char (SIM900.read()));
}
