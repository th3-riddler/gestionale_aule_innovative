<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$email = $_GET["email"] ?? "";
$work = $_GET["work"] ?? "";

if($work == "technician"){
    $query = "SELECT profileImage FROM technician WHERE email = ?";
}
else if($work == "teacher"){
    $query = "SELECT profileImage FROM teacher WHERE email = ?";
}
else{
    echo json_encode(false);
    exit();
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


echo json_encode(count($result) == 0 ? false : base64_encode($result[0]["profileImage"]));

?>