<?php
date_default_timezone_set("Europe/Rome");
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$hour = intval($_GET["hour"] ?? 0);
$weekday = $_GET["weekday"] ?? "";
$room = $_GET["room"] ?? "";
$date = $_GET["date"] ?? "";

$timeHour = array("08" => 1, "09" => 2, "10" => 3, "11" => 4, "12" => 5, "13" => 6, "14" => 7, "15" => 8);

if ($date < date("Y-m-d") || ($date == date("Y-m-d") && ($timeHour[date("H")] >= $hour || $timeHour[date("H")] == null))) {
    header("Location: ../docenti/index.php?error=4");
    exit();
}

$query = "DELETE FROM reservation WHERE date = ? AND room = ? AND weekday = ? AND hour = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssi", $date, $room, $weekday, $hour);

try {
    $stmt->execute();
} catch (Exception $e) {
    header("Location: ../docenti/index.php?error=5");
    exit();
}

header("Location: ../docenti/index.php?date=" . $date);
?>