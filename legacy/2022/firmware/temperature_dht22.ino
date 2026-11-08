#include <DHT.h>
#include"DHT.h"
#define DHTPIN A0
#define DHTTYPE DHT22
DHT dht(DHTPIN,DHTTYPE);
void setup (){
  Serial.begin(9600);
  Serial.println("DHTxx test!");
  dht.begin();
}
void loop(){
  float t;
  t=dht.readTemperature();
  Serial.print("Temperature:");
  Serial.print(t);
  Serial.println("*C");
  delay(1000);
  
}
