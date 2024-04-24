<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$query = "SELECT name, surname, email FROM teacher";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$teachers = array();

foreach ($result as $row) {
    array_push($teachers, [$row["surname"] . " " . $row["name"], $row["email"]]);
}

echo json_encode($teachers);
?>