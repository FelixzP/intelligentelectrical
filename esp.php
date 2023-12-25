<?php

$hostname = "localhost";
$username = "root";
$password = "_39108401_#Pp";
$database = "test99";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Database connection is OK ";

if(isset($_POST["voltage"]) && isset($_POST["current"]) && isset($_POST["power"]) && isset($_POST["energy"]) && isset($_POST["temperature"]) && isset($_POST["humidity"])){

    $v = $_POST["voltage"];
    $c = $_POST["current"];
    $p = $_POST["power"];
    $e = $_POST["energy"];
    $t = $_POST["temperature"];
    $h = $_POST["humidity"];

    $sql = "INSERT INTO plug (voltage, current, power, energy, temperature, humidity) VALUES (".$v.", ".$c.", ".$p.", ".$e.", ".$t.", ".$h.")"; 
    echo $sql;
    if (mysqli_query($conn, $sql)) {
        echo "\nสำเร็จ";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

?>
