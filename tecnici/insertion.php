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

if (isset($_POST["room"])) {
    $_SESSION["current_room"] = $_POST["room"];
    header("Location: insertion.php");
} else {
    if (!isset($_SESSION["current_room"])) {
        $_SESSION["current_room"] = "A1";
    }
}

$rooms = json_decode(file_get_contents("http://127.0.0.1/gestionale_CARRELLI/API/getRooms.php"));
$teachers = json_decode(file_get_contents("http://127.0.0.1/gestionale_CARRELLI/API/getTeachers.php"));
$classes = json_decode(file_get_contents("http://127.0.0.1/gestionale_CARRELLI/API/getClasses.php"));
$schedule = json_decode(file_get_contents("http://127.0.0.1/gestionale_CARRELLI/API/getSchedule.php?room=" . $_SESSION["current_room"]));

$hours = ["8:10 - 9:10", "9:10 - 10:00", "10:10 - 11:10", "11:10 - 12:00", "12:10 - 13:10", "13:10 - 14:05", "14:20 - 15:10", "15:10 - 16:10"];

print_r($schedule);
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
        <li><a id="logout" href="../API/logout.php">[ <-- </a></li>
    </section>

    <section id="main">
        <section id="room_select">
            <form action="" method="POST">
                <select onchange="this.form.submit()" name="room" id="room_select">
                    <?php
                        foreach ($rooms as $room) {
                            if ($room == $_SESSION["current_room"]) {
                                echo "<option selected value='$room'>$room</option>";
                                continue;
                            }
                            echo "<option value='$room'>$room</option>";
                        }
                    ?>
                </select>
            </form>
        </section>
        <section id="insertion">
            <form id="form_hour" action="../API/insertion.php" method="POST">
                <select name="teacher" id="teacher_select">
                    <?php
                        foreach ($teachers as $teacher) {
                            echo "<option value='$teacher[1]'>$teacher[0]</option>";
                        }
                    ?>
                </select>
                <select name="class" id="class_select">
                    <?php
                        foreach ($classes as $class) {
                            echo "<option value='$class'>$class</option>";
                        }
                    ?>
                </select>
                <button type="submit">Submit</button>
            </form>
        </section>
        <section id="schedule">
            <table>
                <tr>
                    <th>Ora</th>
                    <th>Lunedì</th>
                    <th>Martedì</th>
                    <th>Mercoledì</th>
                    <th>Giovedì</th>
                    <th>Venerdì</th>
                    <th>Sabato</th>
                </tr>
                <?php 
                    foreach ($hours as $pos => $hour) {
                        echo "<tr>";
                        echo "<td value=" . $pos + 1 . ">$hour</td>";

                        for($i = 1; $i <= 6; $i++) {
                            echo "<td id=" . ($pos + 1) . $i .  ">";
                            foreach($schedule as $lesson) {
                                if ($lesson->ora == $pos + 1) { //bisogna implementare un controllo anche sul giorno, quello sulle ore funziona
                                    echo "<p>$lesson->nome_docente</p>";
                                    echo "<p>$lesson->numero_classe$lesson->sezione</p>";
                                }
                            }
                            echo "</td>";
                        }

                        echo "</tr>";
                    }
                ?>
            </table>
        </section>
    </section>

    <script src="../javascripts/tecnici.js"></script>
</body>

</html>