#include <WiFi.h> // ลงไลบรารี่ด้วย
#include <HTTPClient.h>
#include <PZEM004Tv30.h> // PZEM004Tv30
#include <HardwareSerial.h> //
#include <Wire.h> //ลงไลบรารี่ด้วย
#include <LiquidCrystal_I2C.h> //lcd_I2C

char ssid[] = "Pirat 2.4G";    //ชื่อWifi ที่จะให้บอร์ดเชื่อมต่อ
char pass[] = "56456740"; //รหัสWifi

///////// DHT22 sensor /////////
#include <DHT.h> 
#define DHTPIN 2
#define DHTTYPE DHT22 
DHT dht22(DHTPIN, DHTTYPE); 
int temperature = 0;
int humidity = 0;
////////////////////////////

#define RX2 16                                  
#define TX2 17
PZEM004Tv30 pzem(&Serial2);
LiquidCrystal_I2C lcd(0x27, 20, 4);

String URL = "https://esp.peeranat.online/esp.php";

void setup() {
  Serial.begin(115200);                       // เริ่มต้นการทำงานของ Serial
  dht22.begin();                           // เริ่มต้นการทำงานของ dht22
  Serial2.begin(9600, SERIAL_8N1, RX2, TX2);  // เริ่มต้นการเชื่อมต่อ PZEM004Tv30
  lcd.begin();                                // เริ่มต้นการทำงาน lcd
  lcd.backlight();
  WiFi.begin(ssid, pass);                     

  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
    Serial.print("connected to : "); Serial.println(ssid);
    Serial.print("IP address: "); Serial.println(WiFi.localIP());
  }
}

void loop() {
  //เช็คว่าต่อ Wi-Fi หรื่อไม่
  if(WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }
  //เก็บค่าจากเซนเซอร์วัดไฟฟ้า
  float voltage = pzem.voltage();  // อ่านข้อมูลและแสดงผลข้อมูลจาก PZEM004Tv30
  if (!isnan(voltage)) {
    Serial.print("แรงดัน: "); Serial.print(voltage); Serial.println("V");
  }
  float current = pzem.current();
  if (!isnan(current)) {
    Serial.print("กระแส: "); Serial.print(current); Serial.println("A");
  }
  float power = pzem.power();
  if (!isnan(power)) {
    Serial.print("กำลัง: "); Serial.print(power); Serial.println("W");
  }
  float energy = pzem.energy();
  if (!isnan(energy)) {
    Serial.print("พลังงานไฟฟ้า: "); Serial.print(energy, 3); Serial.println("หน่วย");
  }
  lcd.begin();
  lcd.backlight();
  lcd.setCursor(0, 0);
  lcd.print("Voltage: ");  lcd.print(voltage ); lcd.print("V");
  lcd.setCursor(0, 1);
  lcd.print("Current: ");  lcd.print(current  ); lcd.print("A");
  lcd.setCursor(0, 2);
  lcd.print("Power  : ");  lcd.print(power); lcd.print("W");
  lcd.setCursor(0, 3);
  lcd.print("Energy : ");  lcd.print(energy   ); lcd.print("kWh");
  Serial.println();
  delay(1000);  // หน่วงเวลา 1 วินาที
  
  // รับค่าเซนเซอร์วัดอุณหภูมิ
  temperature = dht22.readTemperature(); //Celsius
  humidity = dht22.readHumidity();
  //-----------------------------------------------------------
  // เช็คว่าอ่านค่าผิดพลาดหรือไม่.
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Failed to read from DHT sensor!");
    temperature = 0;
    humidity = 0;
  }
  //-----------------------------------------------------------
  Serial.printf("Temperature: %d °C\n", temperature);
  Serial.printf("Humidity: %d %%\n", humidity);
  if (!isnan(voltage) && !isnan(current) && !isnan(power) && !isnan(energy) && !isnan(temperature )&& !isnan(humidity)) {

    String postData = "&voltage=" + String(voltage) + "&current=" + String(current) + "&power=" + String(power) + "&energy=" + String(energy) + "&temperature=" + String(temperature) + "&humidity=" + String(humidity);
  
    HTTPClient http;
    http.begin(URL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
    int httpCode = http.POST(postData);
    String payload = http.getString();

    Serial.print("URL : "); Serial.println(URL); 
    Serial.print("Data: "); Serial.println(postData);
    Serial.print("httpCode: "); Serial.println(httpCode);
    Serial.print("payload : "); Serial.println(payload);
    Serial.println("--------------------------------------------------");
  }
  delay(2000); // หน่วงเวลา 2 วินาที

}

void connectWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  
  WiFi.mode(WIFI_STA);
  
  WiFi.begin(ssid, pass);
  Serial.println("Connecting to WiFi");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
    
  Serial.print("connected to : "); Serial.println(ssid);
  Serial.print("IP address: "); Serial.println(WiFi.localIP());
}