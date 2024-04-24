<?php
header("Content-Type: application/json");
require_once("db.php");

$mailTeacher = $_GET["mail"];

$query = "SELECT * FROM orario_aula WHERE email_docente = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $mailTeacher);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result);
?>