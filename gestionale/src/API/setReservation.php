<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$pc_qt = $_POST["pc_qt"] ?? "";
$teacher_note = $_POST["teacher_note"] ?? "";
$date = $_POST["date"] ?? "";
$weekday = $_POST["weekday"] ?? "";
$room = $_POST["room"] ?? "";
$hour = intval($_POST["hour"] ?? 0);
$cart_id = intval($_POST["cart_id"] ?? 0);

//echo json_encode($_POST);
//exit();

$query = "SELECT (c.pc_max - IFNULL((SELECT SUM(r.pc_qt) FROM reservation r WHERE r.cart_id = c.id AND r.hour = ? AND r.date = ?), 0)) AS remaining_pc FROM cart c WHERE c.id = ?;";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $hour, $date, $cart_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$remaining_pc = $result[0]["remaining_pc"];
$stmt->close();

if ($remaining_pc < $pc_qt) {
    header("Location: ../docenti/index.php?error=2");
    exit();
}

$query = "INSERT INTO reservation (pc_qt, teacher_note, date, weekday, room, hour, cart_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", $pc_qt, $teacher_note, $date, $weekday, $room, $hour, $cart_id);
try {
    $stmt->execute();
} catch (Exception $e) {
    //echo $e;
    header("Location: ../docenti/index.php?error=1");
    exit();
}
$stmt->close();

header("Location: ../docenti/index.php");
?>
