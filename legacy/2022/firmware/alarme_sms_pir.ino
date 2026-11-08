     HNN#include <SoftwareSerial.h> 
SoftwareSerial SIM900A(7, 8);   
String message = "";   
int capteurmouvement = 3;    
int mouvement;
int led=2;

void setup()
{
  Serial.begin(115200); 
  SIM900A.begin(19200); 
  pinMode(capteurmouvement, INPUT);   
  pinMode(led, OUTPUT);
  SIM900A.print("AT+CMGF=1\r");  
  delay(100);
  SIM900A.print("AT+CNMI=2,2,0,0,0\r"); 
  delay(100);
}

void loop()
{
  mouvement=digitalRead(capteurmouvement);
  if(mouvement==HIGH)
  {  
    message = "Avertissement Quelqu'un essaie d'ouvrir la porte de la salle serveur ";
    send_message(message);
    digitalWrite(led,HIGH);
    delay(2000);
  }
  else{digitalWrite(led,LOW);}
} 

void send_message(String message)
{
  SIM900A.println("AT+CMGF=1");    
  delay(100);  
  SIM900A.println("AT+CMGS=\"+216XXXXXXXX\""); 
  delay(100);
  SIM900A.println(message);   
  delay(100);
  SIM900A.println((char)26);  
  delay(100);
  SIM900A.println();
  delay(1000);  
}
