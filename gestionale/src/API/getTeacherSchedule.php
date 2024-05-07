<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$emailTeacher = $_GET["email"] ?? "";

$query = "SELECT * FROM room_schedule WHERE teacher_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $emailTeacher);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result);
?>