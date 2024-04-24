<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$room = $_GET["room"] ?? "A1";

$query = "SELECT * FROM room_schedule WHERE room = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $room);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result);
?>