<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$data = json_decode(file_get_contents("php://input"), true);

$email = $data["teachers"] ?? array();

$query = "DELETE FROM teacher WHERE email IN (";
$types = "";
foreach ($email as $key => $value) {
    $query .= "?,";
    $types .= "s";
}
$query = substr($query, 0, -1);
$query .= ");";


$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$email);
$stmt->execute();

echo json_encode(["status" => "successfully deleted teachers"]);
?>