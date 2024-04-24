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

    $hour = $_GET["hour"] ?? "";
    $lessondate = $_GET["date"] ?? "";
    $cart_id = $_GET["cart_id"] ?? "";

    $query = "SELECT (c.pc_max - IFNULL((SELECT SUM(p.numero_computer) FROM prenotazione p WHERE p.id_carrello = c.id AND p.ora = ? AND p.data = ?), 0)) AS remaining_pc FROM carrello c WHERE c.id = ?;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $hour, $lessondate, $cart_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($result[0]);
?>