<?php
header("Content-Type: application/json");
require_once("db.php");

$query = "SELECT nome_carrello,id FROM carrello";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($result);
?>