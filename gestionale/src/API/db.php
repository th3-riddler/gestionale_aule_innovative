<?php
$hostname = "database";
$username = "root";
$password = "";
$dbname = "gestionale_aule_innovative";

$conn = new mysqli($hostname, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
