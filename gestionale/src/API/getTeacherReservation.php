<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$hour = $_GET["hour"] ?? "";
$lessondate = $_GET["date"] ?? "";
$cart_id = $_GET["cart_id"] ?? "";
$room = $_GET["room"] ?? "";

$query = "SELECT SUM(pc_qt) AS pc_qt FROM reservation WHERE cart_id = ? AND hour = ? AND date = ? AND room = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("isss", $cart_id, $hour, $lessondate, $room);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result[0]);
?>