<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$query = "SELECT * FROM cart";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result);
?>