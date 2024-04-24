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

$token = $_COOKIE["token"];

$hours = ["8:10 - 9:10", "9:10 - 10:00", "10:10 - 11:10", "11:10 - 12:00", "12:10 - 13:10", "13:10 - 14:05", "14:20 - 15:10", "15:10 - 16:10"];

$weekdays_it = array("Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
$weekdays = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

$date = $_GET["data"] ?? date("Y-m-d");

$week_reservations = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getWeekReservations.php?date=" . $date . "&token=" . $token), true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Home Tecnici</title>
</head>

<body>
    <section id="menu">
        <a href="../profile/profile.php">
            <li><?php echo $_SESSION["email"]; ?></li>
        </a>
        <li><?php echo $_SESSION["name"]; ?></li>
        <li><?php echo $_SESSION["surname"]; ?></li>
        <a href="setTeachersSchedule.php">Inserimento</a>
        <a href="setCart.php">Modifica Carrelli</a>
        <li><a id="logout" href="../API/logout.php">[ <-- </a></li>
    </section>

    <form action="../API/setTechnicianNote.php" method="POST" id="formReservation">
        <input name="technician_note" id="technician_note" type="text" placeholder="Nota per il docente">
        <button type="submit">Annota</button>
    </form>

    <section id="main">
        <section id="dateSection">
            <div id="previous"> < </div>
            <div id="current"></div>
            <div id="next"> > </div>
        </section>
        
        <table>
            <tr>
                <th>Ora</th>
                <?php
                    foreach ($weekdays_it as $weekday) {
                        // Calculate the date of the day
                        $pos = array_search(date('l', strtotime($date)), $weekdays);
                        $shift = $pos - array_search($weekday, $weekdays_it);
                        $specificDate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));
                        
                        echo "<th><span class='day'>$weekday</span><br><span class='date'>$specificDate</span></th>";
                    }
                ?>
            </tr>
            <?php 
                foreach ($hours as $pos => $hour) {
                    echo "<tr>";
                    echo "<td value=" . $pos + 1 . ">$hour</td>";

                    for($i = 1; $i <= 6; $i++) {
                        echo "<td id=" . ($pos + 1) . $i .  ">";
                        foreach ($week_reservations as $reservation) {
                            if ($reservation["hour"] == $pos + 1 && $reservation["weekday"] == $weekdays_it[$i - 1]) {
                                echo "<div class='reservation" . ($reservation["technician_note"] ? " reserved" : "") . "'>";
                                echo "<p>PC: <span>" . $reservation["pc_qt"] . "</span></p>";
                                echo "<p>Aula: <span>" . $reservation["room"] . "</span></p>";
                                echo "<p>Nota: <span>" . $reservation["teacher_note"] . "</span></p>";
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