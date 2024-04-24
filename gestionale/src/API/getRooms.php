<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$query = "SELECT Room1, Room2, Room3, Room4, Room5 FROM cart";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$rooms = array();

foreach ($result as $row) {
    foreach ($row as $room) {
        if ($room != null) {
            array_push($rooms, $room);
        }
    }
}

echo json_encode($rooms);
?>