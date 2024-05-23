<?php
date_default_timezone_set("Europe/Rome");
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$data = json_decode(file_get_contents("php://input"), true);

$date = $data["date"] ?? "";
$room = $data["room"] ?? "";
$weekday = $data["weekday"] ?? "";
$hour = intval($data["hour"] ?? 0);

$timeHour = array("08" => 1, "09" => 2, "10" => 3, "11" => 4, "12" => 5, "13" => 6, "14" => 7, "15" => 8);

if ($date < date("Y-m-d") || ($date == date("Y-m-d") && ($timeHour[date("H")] >= $hour || $timeHour[date("H")] == null))) {
    echo json_encode(["status" => "4", "message" => "Non puoi cancellare una prenotazione passata o in corso"]);
    exit();
}

$query = "DELETE FROM reservation WHERE date = ? AND room = ? AND weekday = ? AND hour = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssi", $date, $room, $weekday, $hour);

try {
    $stmt->execute();
} catch (Exception $e) {
    echo json_encode(["status" => "5", "message" => "Errore nella cancellazione della prenotazione"]);
    exit();
}

echo json_encode(["status" => "6", "message" => "Prenotazione cancellata con successo"]);
?>