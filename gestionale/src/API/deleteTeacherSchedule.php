<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$hour = $_GET["hour"];
$weekday = $_GET["weekday"];
$aula = $_GET["aula"];



$query = "DELETE FROM room_schedule WHERE room = ? AND hour = ? AND weekday = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sis", $aula, $hour, $weekday);

try {
    $stmt->execute();
} catch (Exception $e) {
    header("Location: ../tecnici/setTeachersSchedule.php?error=1");
    exit();
}
$stmt->close();

header("Location: ../tecnici/setTeachersSchedule.php?current_room=" . $aula);
?>