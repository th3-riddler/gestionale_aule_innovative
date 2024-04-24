<?php
session_start();


if (!isset($_SESSION["email"])) {
    header("Location: ../index.php");
    exit();
}

if (!$_SESSION["sudo"]) {
    header("Location: ../docenti/index.php");
    exit();
}

$hours = ["8:10 - 9:10", "9:10 - 10:00", "10:10 - 11:10", "11:10 - 12:00", "12:10 - 13:10", "13:10 - 14:05", "14:20 - 15:10", "15:10 - 16:10"];
$days = array("Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
$days_en = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
$date = $_GET["data"] ?? date("Y-m-d");


$prenotazioni_settimana = json_decode(file_get_contents("http://127.0.0.1/API/getWeekReservations.php?data=" . $date), true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Home Tecnici</title>
</head>

<body>
    <section id="menu">
        <a href="../profile/profile.php">
            <li><?php echo $_SESSION["email"]; ?></li>
        </a>
        <li><?php echo $_SESSION["nome"]; ?></li>
        <li><?php echo $_SESSION["cognome"]; ?></li>
        <a href="setTeachersSchedule.php">Inserimento</a>
        <a href="setCart.php">Modifica Carrelli</a>
        <li><a id="logout" href="../API/logout.php">[ <-- </a></li>
    </section>

    <form action="../API/setTechnicianNote.php" method="POST" id="form_prenotazione">
        <input name="nota_tecnico" id="nota_tecnico" type="text" placeholder="nota per il docente">
        <button type="submit">Prenota</button>
    </form>

    <section id="main">
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
                        // Calculate the date of the day
                        $pos = array_search(date('l', strtotime($date)), $days_en);
                        $shift = $pos - array_search($day, $days);
                        $specificDate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));
                        
                        echo "<th><span class='day'>$day</span><br><span class='date'>$specificDate</span></th>";
                    }
                ?>
            </tr>
            <?php 
                foreach ($hours as $pos => $hour) {
                    echo "<tr>";
                    echo "<td value=" . $pos + 1 . ">$hour</td>";

                    for($i = 1; $i <= 6; $i++) {
                        echo "<td id=" . ($pos + 1) . $i .  ">";
                        foreach ($prenotazioni_settimana as $prenotazione) {
                            if ($prenotazione["ora"] == $pos + 1 && $prenotazione["giorno"] == $days[$i - 1]) {
                                echo "<div class='prenotazione'>";
                                echo "<p>PC: <span>" . $prenotazione["numero_computer"] . "</span></p>";
                                echo "<p>Aula: <span>" . $prenotazione["aula"] . "</span></p>";
                                echo "<p>Note: <span>" . $prenotazione["nota_docente"] . "</span></p>";
                                echo "</div>";
                            }
                        }
                        echo "</td>";
                    }

                    echo "</tr>";
                }
            ?>
        </table>

    </section>

    <script src="../javascripts/indexTechnician.js"></script>
</body>
</html>