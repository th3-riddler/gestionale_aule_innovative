<?php
    header("Content-Type: application/json");
    require_once("db.php");

    $nota = $_POST["nota_tecnico"] ?? "";
    $data = $_POST["data"] ?? "";
    $aula = $_POST["aula"] ?? "";
    $giorno = $_POST["giorno"] ?? "";
    $ora = intval($_POST["ora"]) ?? 0;

    $query = "UPDATE prenotazione SET nota_tecnico = ? WHERE data = ? AND aula = ? AND giorno = ? AND ora = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $nota, $data, $aula, $giorno, $ora);
    try {
        $stmt->execute();
    } catch (Exception $e) {
        header("Location: ../tecnici/index.php?error=1");
        exit();
    }
    $stmt->close();
    header("Location: ../tecnici/index.php");
?>