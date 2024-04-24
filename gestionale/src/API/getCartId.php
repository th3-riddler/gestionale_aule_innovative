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

    $room = $_GET["room"] ?? "";

    $query = "SELECT id FROM carrello WHERE (Aula1 = ? OR Aula2 = ? OR Aula3 = ? OR Aula4 = ? OR Aula5 = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $room, $room, $room, $room, $room);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($result[0]);
?>