<?php
header("Content-Type: application/json");
require_once('db.php');
/*session_start();

if (!isset($_SESSION["email"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION["sudo"]) {
    header("Location: ../tecnici/index.php");
    exit();
}*/

$hour = $_GET["hour"] ?? "";
$room = $_GET["room"] ?? "";
$lessondate = $_GET["date"] ?? "";
$cart_id = $_GET["cart_id"] ?? "";

$query = "SELECT nota_tecnico FROM carrello INNER JOIN prenotazione ON carrello.id = prenotazione.id_carrello WHERE carrello.id = ? AND ora = ? AND aula = ? AND data = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("siss", $cart_id, $hour, $room, $lessondate);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result[0]);
?>