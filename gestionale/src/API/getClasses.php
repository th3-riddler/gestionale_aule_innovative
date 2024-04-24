<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$query = "SELECT * FROM class";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$classes = array();

foreach ($result as $row) {
    array_push($classes, $row["year"] . $row["section"]);
}

echo json_encode($classes);
?>