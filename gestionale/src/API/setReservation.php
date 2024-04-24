<?php
header("Content-Type: application/json");
require_once("db.php");

$n_pc = $_POST["n_pc"];
$nota_docente = $_POST["nota_docente"];
$data = $_POST["data"];
$giorno = $_POST["giorno"];
$aula = $_POST["aula"];
$ora = intval($_POST["ora"]);
$cart = intval($_POST["id_carrello"]);

//echo json_encode($_POST);
//exit();

$query = "SELECT (c.pc_max - IFNULL((SELECT SUM(p.numero_computer) FROM prenotazione p WHERE p.id_carrello = c.id AND p.ora = ? AND p.data = ?), 0)) AS remaining_pc FROM carrello c WHERE c.id = ?;";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $ora, $data, $cart);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$remaining_pc = $result[0]["remaining_pc"];
$stmt->close();

if ($remaining_pc < $n_pc) {
    header("Location: ../docenti/index.php?error=2");
    exit();
}

$query = "INSERT INTO prenotazione (numero_computer, nota_docente, data, giorno, aula, ora, id_carrello) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", $n_pc, $nota_docente, $data, $giorno, $aula, $ora, $cart);
try {
    $stmt->execute();
} catch (Exception $e) {
    //echo $e;
    header("Location: ../docenti/index.php?error=1");
    exit();
}
$stmt->close();

header("Location: ../docenti/index.php");
?>
