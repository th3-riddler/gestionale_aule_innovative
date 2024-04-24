<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$technician_note = $_POST["technician_note"] ?? "";
$date = $_POST["date"] ?? "";
$room = $_POST["room"] ?? "";
$weekday = $_POST["weekday"] ?? "";
$hour = intval($_POST["hour"]) ?? 0;

$query = "UPDATE reservation SET technician_note = ? WHERE date = ? AND room = ? AND weekday = ? AND hour = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssi", $technician_note, $date, $room, $weekday, $hour);
try {
    $stmt->execute();
} catch (Exception $e) {
    header("Location: ../tecnici/index.php?error=1");
    exit();
}
$stmt->close();

header("Location: ../tecnici/index.php");
?>