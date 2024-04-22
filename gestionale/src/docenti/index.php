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
                    echo "<th>$day</th>";
                }
            ?>
        </tr>
        <?php 
            foreach ($hours as $pos => $hour) {
                echo "<tr>";
                echo "<td value=" . $pos + 1 . ">$hour</td>";

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


                $query = "SELECT pc_disp, id FROM carrello WHERE Aula1 = ? OR Aula2 = ? OR Aula3 = ? OR Aula4 = ? OR Aula5 = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $room, $room, $room, $room, $room);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();

                $pc_disp = strval($result[0]["pc_disp"]);

                echo "<script>";
                echo "document.getElementById('$hour$day').innerHTML = '$class$section $room <br>pc disponibili $pc_disp';";
                echo "document.getElementById('$hour$day').addEventListener('click', activate);";
                echo "document.getElementById('$hour$day').value = " . $result[0]["id"] . ";";
                echo "</script>";
            }
        ?>
    </table>

    
</body>
</html>
