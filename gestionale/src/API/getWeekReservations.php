<?php
    require_once("db.php");

    $data = $_GET["data"];
    $monday_week = date("Y-m-d", strtotime("monday this week", strtotime($data)));
    $saturday_week = date("Y-m-d", strtotime("saturday this week", strtotime($data)));

    //echo json_encode(array("monday" => $monday_week, "saturday" => $saturday_week));

    $query = "SELECT * FROM prenotazione WHERE data BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $monday_week, $saturday_week);
    $stmt->execute();
    $result = $stmt->get_result();
    $prenotazioni = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($prenotazioni);

?>