#include <WiFi.h> 
#include <MQTT.h>
#include <ESP32Servo.h>
#include <NusabotSimpleTimer.h>
#include "DHTesp.h"

//objec wifi
WiFiClient net;
MQTTClient client;

//object servo
Servo servo;

//object timer
NusabotSimpleTimer timer;

//object sensor dht
DHTesp dhtSensor;

String serial_number = "123456789";

const char ssid[] = "Wokwi-GUEST";
const char pass[] = "";

//variabel pin GPIO
const int pinRed = 26;
const int pinGreen = 27;
const int pinBlue = 25;
const int pinLED = 33;
const int pinServo = 18;
const int pinDHT = 32;

void setup() {
  pinMode(pinRed, OUTPUT);
  pinMode(pinGreen, OUTPUT);
  pinMode(pinBlue, OUTPUT);
  pinMode(pinLED, OUTPUT);
  servo.attach(pinServo, 500,2400);
  dhtSensor.setup(pinDHT, DHTesp::DHT22);

  WiFi.begin(ssid, pass);
  client.begin("bitterprincess291.cloud.shiftr.io", net);

  client.onMessage(subscribe);

  timer.setInterval(2000, publishDHT);

  connect();
}

void loop() {
  client.loop();
  timer.run();

  if(!client.connected()){ //fitur reconnect
    connect();
  }

  delay(20);
}

void publishDHT(){
  TempAndHumidity  data = dhtSensor.getTempAndHumidity();
  client.publish("kandang/"+ serial_number +"/suhu", String(data.temperature, 2), true, 1);
  client.publish("kandang/"+ serial_number +"/lembab", String(data.humidity, 1), true, 1);
}

void subscribe(String &topic, String &data){
  if(topic == "kandang/"+ serial_number +"/lampu"){
    if(data == "nyala"){
      digitalWrite(pinLED, 1);
    } else {
      digitalWrite(pinLED,0);
    }
  }

  if (topic == "kandang/"+ serial_number +"/pakan") {
    if (data == "buka") {
      servo.write(0);
    } else if (data == "tutup") {
      servo.write(90);
    }
  }
}

void rgb(bool red, bool green, bool blue){
  digitalWrite(pinRed, red);
  digitalWrite(pinGreen, green);
  digitalWrite(pinBlue, blue);
}

void connect(){
  rgb(1,0,0); //rgb merah
  while(WiFi.status() != WL_CONNECTED) {
    delay(500);
  }
  rgb(0,1,0); //rbg hijau

  client.setWill("kandang/status/123456789", "offline", true, 1);
  while(!client.connect("abc321001xxx", "bitterprincess291", "Kabere02")){
    delay(500);
  }
  rgb(0,0,1); //rgb biru

  client.publish("kandang/status/123456789", "online", true, 1);
  client.subscribe("kandang/#", 1);
}