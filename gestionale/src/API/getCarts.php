<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$query = "SELECT cart_name, id FROM cart";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($result);
?>