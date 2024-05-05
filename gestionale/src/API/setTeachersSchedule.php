<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

foreach ($_POST["hour"] as $key => $hour) {
    $weekday = $_POST["weekday"][$key] ?? "";
    $class_year = intval(str_split($_POST["class"] ?? "")[0]);
    $class_section = str_split($_POST["class"] ?? "")[1];
    $room = $_POST["room"] ?? "";
    $teacher = $_POST["teacher"] ?? "";

    $query = "INSERT INTO room_schedule VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($query);

    // ("A1", "Lunedì", 1, "letizia.montanari@iticopernico.it", 4, "P")
    $stmt->bind_param("ssisis", $room, $weekday, $hour, $teacher, $class_year, $class_section);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        $query = "UPDATE room_schedule SET teacher_email = ?, class_year = ?, class_section = ? WHERE room = ? AND weekday = ? AND hour = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisssi", $teacher, $class_year, $class_section, $room, $weekday, $hour);
        $stmt->execute();
    }
    $stmt->close();
}

header("Location: ../tecnici/setTeachersSchedule.php?current_room=" . $_POST["room"]);
?>