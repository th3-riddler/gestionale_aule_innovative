<?php
header("Content-Type: application/json");
require_once('db.php');
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$cart_id = $_GET["cart_id"] ?? "";
$hour = $_GET["hour"] ?? "";
$room = $_GET["room"] ?? "";
$date = $_GET["date"] ?? "";

$query = "SELECT technician_note FROM cart INNER JOIN reservation ON cart.id = reservation.cart_id WHERE cart.id = ? AND hour = ? AND room = ? AND date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("siss", $cart_id, $hour, $room, $date);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result[0]);
?>