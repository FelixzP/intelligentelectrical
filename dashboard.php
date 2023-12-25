<?php
$host = 'localhost';
$db   = 'test99';
$user = 'root';
$pass = '_39108401_#Pp';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

$sql = 'SELECT * FROM `plug` ORDER BY `id` DESC LIMIT 10';
$stmt = $pdo->query($sql);
$data = $stmt->fetch();

function getTemperature($data) {
    return $data['temperature'];
}

function getHumidity($data) {
    return $data['humidity'];
}

if (isset($_GET['data'])) {
    if ($_GET['data'] == 'temperature') {
      echo getTemperature($data);
    } else if ($_GET['data'] == 'humidity') {
      echo getHumidity($data);
    }
    exit();
}

$sql = 'SELECT * FROM `plug` ORDER BY `id` DESC LIMIT 15';
$stmt = $pdo->query($sql);
$tableData = $stmt->fetchAll();


if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "SELECT t1.* FROM `plug` t1
            JOIN (
                SELECT `date`, MAX(`energy`) as `max_energy`
                FROM `plug`
                WHERE `date` BETWEEN '$start_date' AND '$end_date'
                GROUP BY DATE(`date`)
            ) t2
            ON t1.`date` = t2.`date` AND t1.`energy` = t2.`max_energy`
            WHERE t1.`id` IN (
                SELECT MAX(`id`)
                FROM `plug`
                WHERE `date` BETWEEN '$start_date' AND '$end_date'
                GROUP BY DATE(`date`)
            )
            ORDER BY t1.`id` DESC";
    $stmt = $pdo->query($sql);
    $tableData = $stmt->fetchAll();

    $sql = "SELECT DATE(`date`) as `date`, MAX(`energy`) as `max_energy` FROM `plug` WHERE `date` BETWEEN '$start_date' AND '$end_date' GROUP BY DATE(`date`)";
    $stmt = $pdo->query($sql);
    $energy_data_this_week = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT DATE(`date`) as `date`, MAX(`energy`) as `max_energy` FROM `plug` WHERE `date` < '$start_date' AND `date` >= DATE_SUB('$start_date', INTERVAL 1 WEEK) GROUP BY DATE(`date`)";
    $stmt = $pdo->query($sql);
    $energy_data_last_week = $stmt->fetchAll(PDO::FETCH_ASSOC);



    $energy_this_week = array_sum(array_column($energy_data_this_week, 'max_energy'));
    $energy_last_week = array_sum(array_column($energy_data_last_week, 'max_energy'));
    $percent_increase = (($energy_this_week - $energy_last_week) / $energy_last_week) * 100;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
$(document).ready(function(){
  setInterval(function(){
    $.get('?data=temperature', function(data) {
      $("#temperature").text("อุณหภูมิ = " + data + "°C ");
    });
    $.get('?data=humidity', function(data) {
      $("#humidity").text("ความชื้น = " + data + "%");
    });
  }, 1000);
});
</script>
</head>

<body style="background-color: BlueViolet;" >
<br>
<p class="text-light fs-1 text-center">โปรแกรมวัดเครื่องมือวัดไฟฟ้าอัจฉริยะวิทยาลัยเทคนิคหาดใหญ่</p>
<div class="container text-center center">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-center">
        <div class="col">
        <form method="post" action="" class="form-group">
            <label for="start_date" class="text-light">เลือกช่วงวันที่:</label>
            <input type="date" name="start_date" class="form-control">
            <br>
            <input type="date" name="end_date" class="form-control">
            <input type="submit" value="Submit" class="btn btn-primary mt-3">
        </form>
        <br>
        <button onclick="window.location.href='dashboard.php'" class="btn btn-primary">กลับไปหน้าเริ่มต้น</button>
        <button onclick="window.location.href='chart.php'" class="btn btn-primary">กราฟ</button>
        </div>
        <br>
        <div class="bg-dark d-flex justify-content-center align-items-center">
        <div>
        <H1 id="temperature" class="text-danger fs-1 text-center">อุณหภูมิ</H1>
        <H1 id="humidity" class="text-danger fs-1 text-center">ความชื้น</H1>
        </div>
        
    </div>

    </div>
</div>

<div class="container">
<!-- <p class="text-light">อุปกรณ์ของเราใช้ไฟเพิ่มขึ้น: <?php echo number_format($percent_increase, 2); ?>% จากช่วงเวลาที่กำหนด</p> -->
<!-- <p class="text-light">กระแสทั้งหมดที่ใช้ : <?php echo number_format($currentweek); ?> จากช่วงเวลาที่กำหนด</p> -->
<br>
  <table class="table table-dark table-striped">
  <tr>
    <th>Date</th>
    <th>Time</th>
    <th>Voltage</th> 
    <th>Power</th>
    <th>Current</th>
    <th>Energy</th> 
    <th>Tempurature</th>
    <th>Humidity</th>

  </tr>
  <?php foreach ($tableData as $row): ?>
  <tr>
    <td><?php echo $row['date']; ?></td>
    <td><?php echo $row['time']; ?></td>
    <td><?php echo $row['voltage']; ?></td>
    <td><?php echo $row['power']; ?></td>
    <td><?php echo $row['current']; ?></td>
    <td><?php echo $row['energy']; ?></td>
    <td><?php echo $row['temperature']; ?></td>
    <td><?php echo $row['humidity']; ?></td>
  </tr>
  <?php endforeach; ?>
  </table>
    </table>
    
</body>
</html>
