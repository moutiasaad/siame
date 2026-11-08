int power=A0;
int led= 2;
int rel1=4;
int rel2=5;
void setup() {
  pinMode(power,INPUT);
  pinMode(led,OUTPUT);
  pinMode(rel1,OUTPUT);
  pinMode(rel2,OUTPUT);

}

void loop() {
  if(digitalRead(power)==LOW)
  {
    digitalWrite(led,HIGH);
    digitalWrite(rel1,HIGH);
    digitalWrite(rel2,HIGH);
    delay(1000);
  }
  else
  {
    digitalWrite(led,LOW);
    digitalWrite(rel1,LOW);
    digitalWrite(rel2,LOW);
  }

}
