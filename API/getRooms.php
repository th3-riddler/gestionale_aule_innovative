<?php

require_once("db.php");
header("Content-Type: application/json");

$query = "SELECT Aula1, Aula2, Aula3, Aula4, Aula5 FROM carrello";

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