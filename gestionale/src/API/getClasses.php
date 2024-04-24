<?php
header("Content-Type: application/json");
require_once("db.php");

$query = "SELECT * FROM classe";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$classes = array();

foreach ($result as $row) {
    array_push($classes, $row["numero"] . $row["sezione"]);
}

echo json_encode($classes);
?>