<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$date = $_GET["date"];
$monday_week = date("Y-m-d", strtotime("monday this week", strtotime($date)));
$saturday_week = date("Y-m-d", strtotime("saturday this week", strtotime($date)));

$query = "SELECT * FROM reservation WHERE date BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $monday_week, $saturday_week);
$stmt->execute();
$result = $stmt->get_result();
$prenotazioni = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($prenotazioni);
?>