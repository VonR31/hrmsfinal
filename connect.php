<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "db_hrms";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "Failed to connect DB" . $conn->connect_error;
}
?>