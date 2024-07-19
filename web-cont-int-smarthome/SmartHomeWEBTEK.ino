#include <WiFi.h>                         // WiFi kütüphanesini projeye ekler
#include <HTTPClient.h>                   // HTTPClient kütüphanesini projeye ekler
#include <HttpClient.h>                   // HttpClient kütüphanesini projeye ekler
#include <Arduino_JSON.h>                 // Arduino_JSON kütüphanesini projeye ekler
#include <ESP32Servo.h>                   // ESP32Servo kütüphanesini projeye ekler
#include <OneWire.h>
#include <DallasTemperature.h>

#define SERIAL_BAUD_RATE 115200           // Seri haberleşme hızını tanımlar

const char* ssid = "EBA";                 // WiFi ağ adı
const char* password = "eba123456";       // WiFi ağ şifresi

// Sunucu adresleri
const char* serverName1 = "http://smarthomeehm.com/esp_outputs_action.php?action=outputs_state&board=1";
const char* serverName2 = "http://smarthomeehm.com/post-esp-data.php";

String apiKeyValue = "tPmAT5Ab3j7F9";     // Sunucuya gönderilecek API anahtarı
const long interval = 1000;               // Veri alım aralığı (milisaniye cinsinden)
unsigned long previousMillis = 0;         // Önceki zaman damgası

String outputsState;                      // Sunucudan alınan çıktıları depolamak için kullanılan değişken

//////////////////////////////////////////////////////////////////////////////////////////////////////
#define GASSENSOR 35                      // Gaz sensörü için pin
int gas_analog_value=0;                   // Yağmur sensörü için analog değer 
// L298N Motor Driver Pins
#define ENA 33                            // Hız kontrolü için PWM pin
#define IN1 25                            // Motor yön kontrolü için IN1 pin
#define IN2 27                            // Motor yön kontrolü için IN1 pin
const int BUZZER = 21;                    // Buzzer'ın bağlı olduğu pin
//////////////////////////////////////////////////////////////////////////////////////////////////////
const int RAINSENSOR = 32;                // Yağmur sensörü için pin
int rain_analog_value=0;                  // Yağmur sensörü için analog değer 
static const int servoPin = 26;
Servo servoMotor;
int currentPosition = 0;                  // Servo motorunun başlangıç konumu
//////////////////////////////////////////////////////////////////////////////////////////////////////
#define LIGHT_SENSOR_PIN 36               // LDR sensörü için pin
int ldr_analog_value;                     // LDR sensörü için analog değer
const int ledOdaPin = 4;                  // Oda ledi için pin
//////////////////////////////////////////////////////////////////////////////////////////////////////
#define TEMPSENSOR 13                     // Sıcaklık sensörü için pin
OneWire oneWire(TEMPSENSOR);
DallasTemperature sensors(&oneWire);
#define RELAY_PIN_HEATER 2                // Isıtıcı için röle pin
// L298N Motor Driver Pins
#define ENB 15                            // Hız kontrolü için PWM pin
#define IN3 14                            // Motor yön kontrolü için IN1 pin
#define IN4 0                             // Motor yön kontrolü için IN1 pin
//////////////////////////////////////////////////////////////////////////////////////////////////////
// Function prototype
String httpGETRequest(const char* serverName);

void setup() {
  Serial.begin(SERIAL_BAUD_RATE);         // Seri haberleşmeyi başlatır

  // WiFi bağlantısı kurulumu
  WiFi.begin(ssid, password);             // WiFi ağına bağlanır
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) {  // Bağlantı sağlanana kadar bekler
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());         // Bağlantı sağlandığında IP adresini seri monitöre yazdırır

  // Pinlerin ayarlanması
  servoMotor.attach(servoPin);             // SERVO
  servoMotor.write(currentPosition);       // Başlangıç konumunu ayarla

  pinMode(RAINSENSOR, INPUT);
  pinMode(GASSENSOR, INPUT);

  pinMode(BUZZER, OUTPUT);                 // Buzzer pinini çıkış olarak ayarla
  pinMode(TEMPSENSOR, INPUT);
  pinMode(ENA, OUTPUT);
  pinMode(IN1, OUTPUT);
  pinMode(IN2, OUTPUT);

  digitalWrite(IN1, LOW);
  digitalWrite(IN2, LOW);

  pinMode(ledOdaPin, OUTPUT);

  pinMode(ENB, OUTPUT);
  pinMode(IN3, OUTPUT);
  pinMode(IN4, OUTPUT);
  pinMode(RELAY_PIN_HEATER, OUTPUT);
    
  digitalWrite(IN3, LOW);
  digitalWrite(IN4, LOW);
}

void loop() {
  unsigned long currentMillis = millis();
  
  if(currentMillis - previousMillis >= interval) {
    if(WiFi.status() == WL_CONNECTED) {   // WiFi bağlantısı varsa devam eder
      
      outputsState = httpGETRequest(serverName1);
      Serial.println(outputsState);
      
      JSONVar myObject = JSON.parse(outputsState);
  
      if(JSON.typeof(myObject) == "undefined") {  
        Serial.println("Parsing input failed!");
        return;
      }
    
      Serial.print("JSON object = ");
      Serial.println(myObject);
    
      JSONVar keys = myObject.keys();
    
      for(int i = 0; i < keys.length(); i++) {
        JSONVar value = myObject[keys[i]];
        Serial.print("GPIO: ");
        Serial.print(keys[i]);
        Serial.print(" - SET to: ");
        Serial.println(value);
        
//////////////////////////////////////////////////////////////////////////////////////////////////////
        if((atoi(keys[i]) == 4 || atoi(keys[i]) == 3 || atoi(keys[i]) == 2) && atoi(value) == 1) {

        //int delayTime = 0; // Varsayılan gecikme süresi
          if(atoi(keys[i]) == 4) {
            // Odanın ışığı high
            analogWrite(ledOdaPin, 255);
           
          }
          else if(atoi(keys[i]) == 3) {
            // Odanın ışığı medium
            analogWrite(ledOdaPin, 170);

          }  
          else if(atoi(keys[i]) == 2) {
             // Odanın ışığı low
            analogWrite(ledOdaPin, 85);

          }  
        }

        else if(atoi(keys[i]) == 1 && atoi(value) == 1) {
          // Odanın ışığını kapat
          analogWrite(ledOdaPin, 0);
          
        }

//////////////////////////////////////////////////////////////////////////////////////////////////////        
       // Eğer servo 0'da ise ve GPIO 4, 5 veya 6'ya ait bir komut alındıysa,
        // servo motorun konumunu güncelle
        if(currentPosition == 0 && (atoi(keys[i]) == 8 || atoi(keys[i]) == 7 || atoi(keys[i]) == 6) && atoi(value)==1) {
          // Her bir GPIO için farklı hızlarla 180 dereceye dön
          int delayTime = 0; // Varsayılan gecikme süresi
          if(atoi(keys[i]) == 8) {
            delayTime = 0;
          }
          else if(atoi(keys[i]) == 7) {
            delayTime = 15;
          }
          else if(atoi(keys[i]) == 6) {
            delayTime = 30;
          }
          for(int j = 0; j < 90; j++) {
            servoMotor.write(j);
            delay(delayTime);
          }
          currentPosition = 90; // Yeni konumu güncelle
        }
        
        // Eğer servo 180 derecede ise ve GPIO 5'ye ait bir komut alındıysa, servo motorun
        // konumunu güncelle
        else if(currentPosition == 90 && atoi(keys[i]) == 5 && atoi(value) == 1) {
          servoMotor.write(0);
          currentPosition = 0; // Yeni konumu güncelle
        }
//////////////////////////////////////////////////////////////////////////////////////////////////////
        if((atoi(keys[i]) == 12 || atoi(keys[i]) == 11 || atoi(keys[i]) == 10) && atoi(value) == 1) {

        //int delayTime = 0; // Varsayılan gecikme süresi
          if(atoi(keys[i]) == 12) {
            // Motor fast
            analogWrite(ENA, 255);  
            digitalWrite(IN1, HIGH);
            digitalWrite(IN2, LOW);
           
          }
          else if(atoi(keys[i]) == 11) {
            // Motor medium
            analogWrite(ENA, 170);  
            digitalWrite(IN1, HIGH);
            digitalWrite(IN2, LOW);
          }  
          else if(atoi(keys[i]) == 10) {
            // Motor slow
            analogWrite(ENA, 85);  
            digitalWrite(IN1, HIGH);
            digitalWrite(IN2, LOW);
          }  
        }
        else if(atoi(keys[i]) == 9 && atoi(value) == 1) {
          // Motor kapat
          analogWrite(ENA, 0);  
          digitalWrite(IN1, HIGH);
          digitalWrite(IN2, LOW);
          
        }
        
        // Buzzer kontrolü
        if(atoi(keys[i]) == 13 && atoi(value) == 1) { 
            // Buzzer'ı aç
            digitalWrite(BUZZER, HIGH);
         } 
         else if(atoi(keys[i]) == 19 && atoi(value) == 1) {
            // Buzzer'ı kapat
            digitalWrite(BUZZER, LOW);
         }
//////////////////////////////////////////////////////////////////////////////////////////////////////
        if((atoi(keys[i]) == 17 || atoi(keys[i]) == 16 || atoi(keys[i]) == 15) && atoi(value) == 1) {

        //int delayTime = 0; // Varsayılan gecikme süresi
          if(atoi(keys[i]) == 17) {
            // Motor fast
            analogWrite(ENB, 255);  
            digitalWrite(IN3, HIGH);
            digitalWrite(IN4, LOW);
           
          }
          else if(atoi(keys[i]) == 16) {
            // Motor medium
            analogWrite(ENB, 170);  
            digitalWrite(IN3, HIGH);
            digitalWrite(IN4, LOW);
          }  
          else if(atoi(keys[i]) == 15) {
            // Motor slow
            analogWrite(ENB, 85);  
            digitalWrite(IN3, HIGH);
            digitalWrite(IN4, LOW);
          }  
        }
        else if(atoi(keys[i]) == 14 && atoi(value) == 1) {
          // Motor kapat
          analogWrite(ENB, 0);  
          digitalWrite(IN3, HIGH);
          digitalWrite(IN4, LOW);
          
        }
        
        // Isıtıcı kontrolü
        if(atoi(keys[i]) == 18 && atoi(value) == 1) {
            // Isıtıcı'ı aç
            digitalWrite(RELAY_PIN_HEATER, HIGH);
          } 
        else if(atoi(keys[i]) == 20 && atoi(value) == 1) {
            // Isıtıcı'ı kapat
            digitalWrite(RELAY_PIN_HEATER, LOW);
          }
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////


      }
      // Son HTTP GET isteğini kaydet
      previousMillis = currentMillis;

       postSensorData(serverName2);
    } 
    else {
      Serial.println("WiFi Disconnected");
    }
  }

String httpGETRequest(const char* serverName1) {
  WiFiClient client;
  HTTPClient http;
    
  // IP adresiniz veya URL'nizle birlikte yolu ekleyin
  http.begin(client, serverName1);

  // HTTP GET isteği gönderin
  int httpResponseCode = http.GET();
  
  String payload = "{}"; 
  
  if (httpResponseCode > 0) {
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
    payload = http.getString();
  } else {
    Serial.print("Error code: ");
    Serial.println(httpResponseCode);
  }
  // Kaynakları serbest bırakın
  http.end();

  return payload;
}


 void postSensorData(const char* serverName2) {

  int ldr_analog_value = analogRead(LIGHT_SENSOR_PIN);  // LDR sensörü değerini oku
  int rain_analog_value = analogRead(RAINSENSOR);       // Yağmur sensörü değerini oku
  int gas_analog_value = analogRead(GASSENSOR);         // Gaz sensörü değerini oku
  /*int temp_voltage = analogRead(TEMPSENSOR) * (3.3 / 4095.0);
  float temp_analog_value = temp_voltage / 0.01;*/
  sensors.requestTemperatures();                        // Sıcaklık sensöründen veri talep et
  float temp_analog_value = sensors.getTempCByIndex(0); // Sıcaklık değerini derece cinsine dönüştür
  
/*
  // Sensör değerlerinin açıklamalarını al
  String LDRsensorValueDescription = getSensorValueDescription(ldr_analog_value, "LDR");
  String GASsensorValueDescription = getSensorValueDescription(gas_analog_value, "GAS");
  String TEMPsensorValueDescription = getSensorValueDescription(temp_analog_value, "TEMP");
  String RAINsensorValueDescription = getSensorValueDescription(rain_analog_value, "RAIN");
*/
  if(WiFi.status() == WL_CONNECTED){
    WiFiClient client;
    HTTPClient http;
    
    http.begin(client, serverName2);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    

//    String httpRequestData = "api_key=" + apiKeyValue + "&sensor=sensors" + "&value1=" + LDRsensorValueDescription + "&value2=" + RAINsensorValueDescription + "&value3=" + GASsensorValueDescription + "&value4=" +TEMPsensorValueDescription;

    String httpRequestData = "api_key=" + apiKeyValue + "&sensor=sensors" + "&value1=" + String(ldr_analog_value) + "&value2=" + String(rain_analog_value) + "&value3=" + String(gas_analog_value) + "&value4=" +String(temp_analog_value);


    int httpResponseCode = http.POST(httpRequestData);
    
    http.end();
  } 
}

/*
String getSensorValueDescription(int sensorValue, String sensorType) {
  if (sensorType == "LDR") {
    if (sensorValue >= 0 && sensorValue <= 450) {
      return "low(" + String(sensorValue) + ")";
    } else if (sensorValue > 450 && sensorValue <= 1800) {
      return "med(" + String(sensorValue) + ")";
    } else if (sensorValue > 1800 && sensorValue <= 4095) {
      return "high(" + String(sensorValue) + ")";
    }
  } else if (sensorType == "RAIN") {
    if (sensorValue >= 0 && sensorValue <= 450) {
      return "low(" + String(sensorValue) + ")";
    } else if (sensorValue > 450 && sensorValue <= 2000) {
      return "med(" + String(sensorValue) + ")";
    } else if (sensorValue > 2000 && sensorValue <= 5000) {
      return "high(" + String(sensorValue) + ")";
    }
  } else if (sensorType == "GAS") {
    if (sensorValue >= 0 && sensorValue <= 100) {
      return "low(" + String(sensorValue) + ")";
    } else if (sensorValue > 100 && sensorValue <= 600) {
      return "med(" + String(sensorValue) + ")";
    } else if (sensorValue > 700 && sensorValue <= 2000) {
      return "high(" + String(sensorValue) + ")";
    }
  } else if (sensorType == "TEMP") {
    if (sensorValue >= 0 && sensorValue <= 16) {
      return "low(" + String(sensorValue) + ")";
    } else if (sensorValue > 16 && sensorValue <= 22) {
      return "med(" + String(sensorValue) + ")";
    } else if (sensorValue > 22 && sensorValue <= 100) {
      return "high(" + String(sensorValue) + ")";
    }
  } 
  return "";
}*/
