<?php
session_start();
require_once("db.php");

$result = $_POST;
$result["room"] = $_SESSION["current_room"];

foreach ($result["hour"] as $key => $hour) {
    $day = $result["day"][$key];
    $class_number = intval(str_split($result["class"])[0]);
    $class_section = str_split($result["class"])[1];

    $query = "INSERT INTO orario_aula VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($query);

    $payload0 = array($result["room"], $day, $hour, $result["teacher"], $class_number, $class_section);

    $stmt->bind_param("ssisis", ...$payload0);
    try {
        $stmt->execute();
    } catch (Exception $e) {
        $payload1 = array($result["teacher"], $class_number, $class_section);

        $query = "UPDATE orario_aula SET email_docente = ?, numero_classe = ?, sezione = ? WHERE aula = ? AND giorno = ? AND ora = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisssi", ...array_merge($payload1, array_diff($payload0, $payload1))); // array_diff returns the elements of payload0 that are not in payload1
        $stmt->execute();
    }
    $stmt->close();
}


header("Location: ../tecnici/setTeachersSchedule.php");

?>