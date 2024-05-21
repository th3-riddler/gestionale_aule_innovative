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
    
    $query = "SELECT SUM(pc_qt) AS teacher_pc FROM reservation WHERE teacher_email = ? UNION SELECT SUM(pc_qt) AS teacher_pc FROM reservation";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $pc = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($pc) == 1) {
        $completeStast["pc"] = array(
            "teacher_pc" => $pc[0]["teacher_pc"],
            "percentage" => 100.00
        );
    } else {
        $completeStast["pc"] = array(
            "teacher_pc" => $pc[0]["teacher_pc"] ?? 0,
            "percentage" => number_format($pc[0]["teacher_pc"] / $pc[1]["teacher_pc"] * 100, 2)
        );
    }

    // get the total number of reservations made by the teacher
    $query = "SELECT COUNT(*) AS counts FROM reservation where teacher_email = ? UNION SELECT COUNT(*) AS counts FROM reservation";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($reservations) == 1) {
        $completeStast["reservation"] = array(
            "teacher_reservation" => $reservations[0]["counts"],
            "percentage" => 100.00
        );
    } else {
        $completeStast["reservation"] = array(
            "teacher_reservation" => $reservations[0]["counts"],
            "percentage" => number_format($reservations[0]["counts"] / $reservations[1]["counts"] * 100, 2)
        );
    }

    // get percentage of reservations made by the teacher that have date in the past (completed)
    $query = "SELECT COUNT(*) AS counts FROM reservation WHERE teacher_email = ? AND date < CURDATE() UNION SELECT COUNT(*) AS counts FROM reservation WHERE teacher_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


    // if there is only 1 row, it means that the teacher has made only reservations in the past (100% completed), otherwise it has made reservations in the future too and so we need to calculate the percentage
    if (count($result) == 1) {
        $completeStast["completed"] = array(
            "completed" => $result[0]["counts"] ?? 0,
            "uncompleted" => 0,
            "percentage" => $reservations[0]["counts"] == 0 ? "-- " : 100.00
        );
    } else {
        $completeStast["completed"] = array(
            "completed" => $result[0]["counts"] ?? 0,
            "uncompleted" => $result[1]["counts"] - $result[0]["counts"],
            "percentage" => number_format($result[0]["counts"] / $result[1]["counts"] * 100, 2)
        );
    }
}

pcStats($conn, $email);

echo json_encode($completeStast);
?>