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

if(isset($_POST["jobType"]) && isset($_POST["location"]) && isset($_POST["department"]) && isset($_POST["issue"]) && isset($_POST["contactNumber"]) && isset($_POST["note"])){

    $jobType = $_POST["jobType"];
    $location = $_POST["location"];
    $department = $_POST["department"];
    $issue = $_POST["issue"];
    $contactNumber = $_POST["contactNumber"];
    $note = $_POST["note"];

    $sql = "INSERT INTO itsupport (jobType, location, department, issue, contactNumber, note) VALUES ('".$jobType."', '".$location."', '".$department."', '".$issue."', '".$contactNumber."', '".$note."')"; 
    echo $sql;
    if (mysqli_query($conn, $sql)) {
        echo "\nสำเร็จ";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

?>
