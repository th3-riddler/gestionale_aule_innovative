<?php
require_once("../API/db.php");
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION["sudo"]) {
    header("Location: ../tecnici/index.php");
    exit();
}



$hours = ["8:10 - 9:10", "9:10 - 10:00", "10:10 - 11:10", "11:10 - 12:00", "12:10 - 13:10", "13:10 - 14:05", "14:20 - 15:10", "15:10 - 16:10"];
$days = array("Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
$days_en = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
$date = $_GET["data"] ?? date("Y-m-d");

$teacherSchedule = json_decode(file_get_contents("http://127.0.0.1/API/getTeachersSchedule.php?mail=" . $_SESSION["email"]));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../javascripts/docenti.js"></script>
    <title>Home Docenti</title>
</head>
<body>
    <section id="userInfo">
        <a href="../profile/profile.php"><li><?php echo $_SESSION["email"]; ?></li></a>
        <li><?php echo $_SESSION["nome"]; ?></li>
        <li><?php echo $_SESSION["cognome"]; ?></li>
        <a href="../API/logout.php">[ <-- </a>
    </section>

    <form action="../API/setReservation.php" method="POST" id="form_prenotazione">
        <input name="n_pc" id="inp_n_pc" type="number" step = "1" min = "0" placeholder="pc da prenotare">
        <input name="nota_docente" id="nota_docente" type="text" placeholder="nota per il tecnico">
        <button type="submit">Prenota</button>
    </form>
    

    <section id="date_sect">
        <div id="previous"> < </div>
        <div id="current"></div>
        <div id="next"> > </div>
    </section>
    
    <table>
        <tr>
            <th>Ora</th>
            <?php
                foreach ($days as $day) {
                    $pos = array_search(date('l', strtotime($date)), $days_en);
                    $shift = $pos - array_search($day, $days);
                    $specificdate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));
                    
                    echo "<th><span>$day</span><br>$specificdate</th>";
                }
            ?>
        </tr>
        <?php 
            foreach ($hours as $pos => $hour) {
                echo "<tr><td value=" . $pos + 1 . ">$hour</td>";

                for($i = 1; $i <= 6; $i++) {
                    echo "<td id=" . ($pos + 1) . $i .  ">";
                    echo "</td>";
                }

                echo "</tr>";
            }

            foreach ($teacherSchedule as $lesson) {
                $hour = $lesson->ora;
                $day = strval(array_search($lesson->giorno, $days) + 1);
                $class = $lesson->numero_classe;
                $section = $lesson->sezione;
                $room = $lesson->aula;

                $pos = array_search(date('l', strtotime($date)), $days_en);
                $shift = $pos - array_search($lesson->giorno, $days);
                $lessondate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));

                $query = "SELECT pc_max, id FROM carrello WHERE (Aula1 = ? OR Aula2 = ? OR Aula3 = ? OR Aula4 = ? OR Aula5 = ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $room, $room, $room, $room, $room);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $cart_max_pc = $result[0]["pc_max"];
                $cart_id = $result[0]["id"];
                $stmt->close();

                $query = "SELECT pc_disp, nota_tecnico, aula FROM carrello INNER JOIN prenotazione ON carrello.id = prenotazione.id_carrello WHERE carrello.id = ? AND ora = ? AND giorno = ? AND data = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("siss", $cart_id, $hour, $lesson->giorno, $lessondate);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();

                $final_pc_number = (empty($result) ? $cart_max_pc : $result[0]["pc_disp"]);

                $index_reservation = array_search($room, array_column($result, "aula"));
                $final_note = ($index_reservation === false ? "" : $result[$index_reservation]["nota_tecnico"]);

                echo "<script>document.getElementById('$hour$day').innerHTML = '$class$section $room <br>pc disponibili $final_pc_number<br>nota tecnico: $final_note';document.getElementById('$hour$day').addEventListener('click', activate);document.getElementById('$hour$day').value=$cart_id;</script>";
            }
        ?>
    </table>

    
</body>
</html>
