<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$email = $_GET["email"] ?? "";
$work = checkUserAuth($email, $token);

if($work == "technician"){
    $query = "UPDATE technician SET profileImage = NULL WHERE email = ?";
}
else if($work == "teacher"){
    $query = "UPDATE teacher SET profileImage = NULL WHERE email = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();

if($work == "technician"){
    header("Location: ../tecnici/technicianProfile.php");
}
else if($work == "teacher"){
    header("Location: ../docenti/teacherProfile.php");
}
?>