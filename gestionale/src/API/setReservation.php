<?php
date_default_timezone_set("Europe/Rome");
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$pc_qt = $_POST["pc_qt"] ?? "";
$teacher_note = $_POST["teacher_note"] ?? "";
$date = $_POST["date"] ?? "";
$weekday = $_POST["weekday"] ?? "";
$room = $_POST["room"] ?? "";
$hour = intval($_POST["hour"] ?? 0);
$cart_id = intval($_POST["cart_id"] ?? 0);
$teacher_email = $_POST["teacher_email"] ?? "";

$timeHour = array("08" => 1, "09" => 2, "10" => 3, "11" => 4, "12" => 5, "13" => 6, "14" => 7, "15" => 8);

if($date < date("Y-m-d") || ($date == date("Y-m-d") && ($timeHour[date("H")] >= $hour || $timeHour[date("H")] == null))) {
    header("Location: ../docenti/index.php?error=3");
    exit();
}


$query = "SELECT (c.pc_max - IFNULL((SELECT SUM(r.pc_qt) FROM reservation r WHERE r.cart_id = c.id AND r.hour = ? AND r.date = ?), 0)) AS remaining_pc FROM cart c WHERE c.id = ?;";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $hour, $date, $cart_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$remaining_pc = $result[0]["remaining_pc"];
$stmt->close();

if ($remaining_pc < $pc_qt) {
    header("Location: ../docenti/index.php?error=2");
    exit();
}

$query = "INSERT INTO reservation (pc_qt, teacher_note, date, weekday, room, hour, cart_id, teacher_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssiis", $pc_qt, $teacher_note, $date, $weekday, $room, $hour, $cart_id, $teacher_email);
try {
    $stmt->execute();
} catch (Exception $e) {
    //echo $e;
    header("Location: ../docenti/index.php?error=1");
    exit();
}
$stmt->close();

header("Location: ../docenti/index.php?date=" . $date);
?>
