<?php
header("Content-Type: application/json");
require_once('db.php');
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$room = $_GET["room"] ?? "";

$query = "SELECT id FROM cart WHERE (Room1 = ? OR Room2 = ? OR Room3 = ? OR Room4 = ? OR Room5 = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssss", $room, $room, $room, $room, $room);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result[0]);
?>