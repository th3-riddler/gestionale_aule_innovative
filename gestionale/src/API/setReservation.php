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

// Aggiornamento disponibilità pc (no < 0)
$query = "UPDATE carrello SET pc_disp = pc_disp - $n_pc WHERE id = $cart AND pc_disp >= $n_pc";
$stmt = $conn->prepare($query);
$stmt->execute();
if ($stmt->affected_rows == 0) {
    echo $stmt->affected_rows;
    //header("Location: ../docenti/index.php?error=1");
    exit();
}
$stmt->close();

$query = "INSERT INTO prenotazione (numero_computer, nota_docente, data, giorno, aula, ora, id_carrello) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", $n_pc, $nota_docente, $data, $giorno, $aula, $ora, $cart);
try {
    $stmt->execute();
} catch (Exception $e) {
    echo $e;
    //header("Location: ../docenti/index.php?error=1");
    exit();
}
$stmt->close();

// Creazione evento per la cancellazione automatica della prenotazione
/*$query = "
CREATE EVENT IF NOT EXISTS event_$cart
ON SCHEDULE AT " . $data . " " . $ora . ":00:00
DO BEGIN
    UPDATE carrello SET pc_disp = pc_disp + $n_pc WHERE id = $cart;
    DELETE FROM prenotazione WHERE id_carrello = $cart AND ora = $ora AND data = '$data';
END";
$stmt = $conn->prepare($query);
$stmt->execute();*/

header("Location: ../docenti/index.php");
?>
