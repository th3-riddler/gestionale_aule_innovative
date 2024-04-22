<?php

require_once("db.php");
header("Content-Type: application/json");

$query = "SELECT nome, cognome, email FROM docente";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$teachers = array();

foreach ($result as $row) {
    array_push($teachers, [$row["cognome"] . " " . $row["nome"], $row["email"]]);
}

echo json_encode($teachers);
?>