<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$email = $_GET["email"] ?? "";

$completeStast = array();

function pcStats($conn, $email) {
    global $completeStast;
    $totalPcQuery = "SELECT SUM(pc_qt) AS pc_tot FROM reservation";
    $stmt = $conn->prepare($totalPcQuery);
    $stmt->execute();
    $totalPc = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $query = "SELECT SUM(pc_qt) AS teacher_pc FROM reservation WHERE teacher_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $completeStast["pc"] = array(
        "teacher_pc" => $result[0]["teacher_pc"] ?? 0,
        "percentage" => number_format($result[0]["teacher_pc"] / $totalPc[0]["pc_tot"] * 100, 2)
    );
}

pcStats($conn, $email);

echo json_encode($completeStast);
?>