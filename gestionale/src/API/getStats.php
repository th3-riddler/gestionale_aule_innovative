<?php
header("Content-Type: application/json");
require_once("db.php");
//require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
//validateToken($token);

$email = $_GET["email"] ?? "";

$query = "SELECT SUM(pc_qt) AS pc_tot FROM reservation WHERE teacher_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($result);
?>