<!DOCTYPE html>
<html>
<head>
    <title>กราฟ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
       <div class=''> <h1 class="text-center my-2">โปรแกรมวัดเครื่องมือวัดไฟฟ้าอัจฉริยะวิทยาลัยเทคนิคหาดใหญ่</h1>
        <h1 class="text-center my-2">Data from Sensors</h1></div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <canvas id="voltageChart"></canvas>
            </div>
            <div class="col-md-8">
                <canvas id="currentChart"></canvas>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <canvas id="powerChart"></canvas>
            </div>
            <div class="col-md-8">
                <canvas id="energyChart"></canvas>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function(){
        $.get("getData.php", function(data, status){
            var labels = data.map(function(item) {
            var date = new Date(item.date + 'T' + item.time);
                return date.toISOString().split('T')[0]; // แสดงเฉพาะวันที่
            });
            var voltage = data.map(function(item) {
                return item.voltage;
            });
            var current = data.map(function(item) {
                return item.current;
            });
            var power = data.map(function(item) {
                return item.power;
            });
            var energy = data.map(function(item) {
                return item.energy;
            });

            var createChart = function(canvasId, label, data, color) {
                var ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            borderColor: color,
                            backgroundColor: color,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: 'ข้อมูลจาก getData.php (' + label + ')'
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            };

            createChart('voltageChart', 'Voltage (V)', voltage, 'rgb(255, 99, 132)');
            createChart('currentChart', 'Current (A)', current, 'rgb(75, 192, 192)');
            createChart('powerChart', 'Power (W)', power, 'rgb(255, 205, 86)');
            createChart('energyChart', 'Energy (kWh)', energy, 'rgb(204, 255, 204)');
        });
    });
    </script>
    <br>
    <div class='d-flex justify-content-center'>
            <button onclick="window.location.href='dashboard.php'" class="btn btn-primary">กลับไปหน้าแรก</button>
    </div>
</body>
</html>
