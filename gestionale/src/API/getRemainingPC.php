<?php 
header("Content-Type: application/json");
require_once('db.php');
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$hour = $_GET["hour"] ?? "";
$lessondate = $_GET["date"] ?? "";
$cart_id = $_GET["cart_id"] ?? "";

$query = "SELECT (c.pc_max - IFNULL((SELECT SUM(r.pc_qt) FROM reservation r WHERE r.cart_id = c.id AND r.hour = ? AND r.date = ?), 0)) AS remaining_pc FROM cart c WHERE c.id = ?;";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $hour, $lessondate, $cart_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result[0]);
?>