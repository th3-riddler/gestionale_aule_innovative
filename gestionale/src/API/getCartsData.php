<?php
    require_once("db.php");

    $query = "SELECT * FROM carrello";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($result);
?>