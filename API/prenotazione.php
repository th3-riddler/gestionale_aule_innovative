<?php
require_once("db.php");

$n_pc = $_POST["n_pc"];
$nota_docente = $_POST["nota_docente"];
$data = $_POST["data"];
$giorno = $_POST["giorno"];
$aula = $_POST["aula"];
$ora = intval($_POST["ora"]);
$cart = intval($_POST["id_carrello"]);

echo $n_pc;
echo $nota_docente;
echo $data;
echo $giorno;
echo $aula;
echo $ora;
echo $cart;



$query = "INSERT INTO prenotazione (numero_computer, nota_docente, data, giorno, aula, ora, id_carrello) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", $n_pc, $nota_docente, $data, $giorno, $aula, $ora, $cart);
$stmt->execute();
$stmt->close();

$query = "UPDATE carrello SET pc_disp = pc_disp - ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $n_pc, $cart);
$stmt->execute();
$stmt->close();

header("Location: ../docenti/index.php");
?>
