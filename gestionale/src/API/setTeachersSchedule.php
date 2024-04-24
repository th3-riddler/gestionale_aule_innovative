<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$result = $_POST;

foreach ($result["hour"] as $key => $hour) {
    $weekday = $result["weekday"][$key];
    $class_year = intval(str_split($result["class"])[0]);
    $class_section = str_split($result["class"])[1];

    $query = "INSERT INTO room_schedule VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($query);

    $payload0 = array($result["room"], $weekday, $hour, $result["teacher"], $class_year, $class_section);

    $stmt->bind_param("ssisis", ...$payload0);
    try {
        $stmt->execute();
    } catch (Exception $e) {
        $payload1 = array($result["teacher"], $class_year, $class_section);

        $query = "UPDATE room_schedule SET teacher_email = ?, class_year = ?, class_section = ? WHERE room = ? AND weekday = ? AND hour = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisssi", ...array_merge($payload1, array_diff($payload0, $payload1))); // array_diff returns the elements of payload0 that are not in payload1
        $stmt->execute();
    }
    $stmt->close();
}

header("Location: ../tecnici/setTeachersSchedule.php");
?>