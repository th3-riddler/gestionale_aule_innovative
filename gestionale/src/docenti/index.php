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

$token = $_COOKIE["token"];

$hours = ["8:10 - 9:10", "9:10 - 10:00", "10:10 - 11:10", "11:10 - 12:00", "12:10 - 13:10", "13:10 - 14:05", "14:20 - 15:10", "15:10 - 16:10"];

$weekdays_it = array("Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
$weekdays = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

$date = $_GET["date"] ?? date("Y-m-d");

$teacherSchedule = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeacherSchedule.php?email=" . $_SESSION["email"] . "&token=" . $token));

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
        <li><?php echo $_SESSION["name"]; ?></li>
        <li><?php echo $_SESSION["surname"]; ?></li>
        <a href="../API/logout.php">[ <-- </a>
    </section>

    <form action="../API/setReservation.php" method="POST" id="formReservation">
        <input name="pc_qt" id="inputPcQt" type="number" step = "1" min = "0" placeholder="Quantitá di PC da prenotare">
        <input name="teacher_note" id="teacherNote" type="text" placeholder="Nota per il tecnico">
        <button type="submit">Prenota</button>
    </form>
    

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
                    // Calculate the date of the weekday
                    $pos = array_search(date('l', strtotime($date)), $weekdays);
                    $shift = $pos - array_search($weekday, $weekdays_it);
                    $specificDate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));
                    
                    echo "<th><span>$weekday</span><br>$specificDate</th>";
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
                $hour = $lesson->hour;
                $weekdayNumber = strval(array_search($lesson->weekday, $weekdays_it) + 1);
                $class = $lesson->class_year;
                $section = $lesson->class_section;
                $room = $lesson->room;

                // Calculate the date of the lesson
                $pos = array_search(date('l', strtotime($date)), $weekdays);
                $shift = $pos - array_search($lesson->weekday, $weekdays_it);
                $lessonDate = date('Y-m-d', strtotime($date . ($shift > 0 ? ' - ' . $shift : ' + ' . -$shift) . ' days'));

                // Get the cart id
                $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getCartId.php?room=" . $room . "&token=" . $token));
                $cart_id = $result->id;

                // Get the remaining PCs in the cart
                $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getRemainingPC.php?hour=" . $hour . "&date=" . $lessonDate . "&cart_id=" . $cart_id . "&token=" . $token));
                $final_pc_number = $result->remaining_pc;

                // Get the technician note for the reservation
                $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTechnicianNote.php?hour=" . $hour . "&room=" . $room . "&date=" . $lessonDate . "&cart_id=" . $cart_id . "&token=" . $token));
                $final_note = ($result->nota_tecnico) ?? "";

                $had_reservation = (empty($result)) ? false : true;

                // Add the values to the object that will be used in the script
                $scriptValues[] = ["hour" => $hour, "weekdayNumber" => $weekdayNumber, "class" => $class, "section" => $section, "room" => $room, "final_pc_number" => $final_pc_number, "final_note" => $final_note, "cart_id" => $cart_id, "had_reservation" => $had_reservation];
            }
        ?>
    </table>

    <?php echo "<script>var scriptValues = " . json_encode($scriptValues) . ";</script>"; ?>
    <script src="../javascripts/indexTeachers.js"></script>
</body>
</html>
