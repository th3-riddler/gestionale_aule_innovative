<?php
    require_once("db.php");

    $room = $_GET["room"] ?? "A1";

    $query = "SELECT * FROM orario_aula WHERE aula = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $room);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($result);
?>