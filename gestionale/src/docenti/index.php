<?php
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

$teacherSchedule = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeachersSchedule.php?mail=" . $_SESSION["email"]));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../css/style.css">
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
                    // Calculate the date of the day
                    $pos = array_search(date('l', strtotime($date)), $days_en);
                    $shift = $pos - array_search($day, $days);
                    $specificDate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));
                    
                    echo "<th><span>$day</span><br>$specificDate</th>";
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

            $scriptValues = [];
            foreach ($teacherSchedule as $lesson) {
                $hour = $lesson->ora;
                $day = strval(array_search($lesson->giorno, $days) + 1);
                $class = $lesson->numero_classe;
                $section = $lesson->sezione;
                $room = $lesson->aula;

                // Calculate the date of the lesson
                $pos = array_search(date('l', strtotime($date)), $days_en);
                $shift = $pos - array_search($lesson->giorno, $days);
                $lessondate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));

                // Get the cart id
                $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getCartId.php?room=" . $room));
                $cart_id = $result->id;

                // Get the remaining PCs in the cart
                $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getRemainingPC.php?hour=" . $hour . "&date=" . $lessondate . "&cart_id=" . $cart_id));
                $final_pc_number = $result->remaining_pc;

                // Get the technician note for the reservation
                $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTechnicianNote.php?hour=" . $hour . "&room=" . $room . "&date=" . $lessondate . "&cart_id=" . $cart_id));
                $final_note = ($result->nota_tecnico) ?? "";

                $had_reservation = (empty($result)) ? false : true;

                // Add the values to the object that will be used in the script
                $scriptValues[] = ["hour" => $hour, "day" => $day, "class" => $class, "section" => $section, "room" => $room, "final_pc_number" => $final_pc_number, "final_note" => $final_note, "cart_id" => $cart_id, "had_reservation" => $had_reservation];
            }
        ?>
    </table>

    <?php echo "<script>var scriptValues = " . json_encode($scriptValues) . ";</script>"; ?>
    <script src="../javascripts/indexTeachers.js"></script>
</body>
</html>
