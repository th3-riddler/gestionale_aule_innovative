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
    header("Location: setTeachersSchedule.php");
} else {
    if (!isset($_SESSION["current_room"])) {
        $_SESSION["current_room"] = "A1";
    }
}

$token = $_COOKIE["token"];

$rooms = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getRooms.php" . "?token=" . $token));
$teachers = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeachers.php" . "?token=" . $token));
$classes = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getClasses.php" . "?token=" . $token));
$schedule = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getSchedule.php?room=" . $_SESSION["current_room"] . "&token=" . $token));

$hours = ["8:10 - 9:10", "9:10 - 10:00", "10:10 - 11:10", "11:10 - 12:00", "12:10 - 13:10", "13:10 - 14:05", "14:20 - 15:10", "15:10 - 16:10"];
$weekdays = ["Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"];
?>

<!DOCTYPE html>
<html lang="it">

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
        <li><a id="logout" href="../API/logout.php">[ <-- </a></li>
    </section>

    <section id="main">
        <section id="roomSelectSection">
            <form action="" method="POST">
                <select onchange="this.form.submit()" name="room" id="roomSelect">
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
            <form id="formHour" action="../API/setTeachersSchedule.php" method="POST">
                <input type="hidden" name="room" value="<?php echo $_SESSION["current_room"]; ?>">
                <select name="teacher" id="teacherSelect">
                    <?php
                        foreach ($teachers as $teacher) {
                            echo "<option value='$teacher[1]'>$teacher[0]</option>";
                        }
                    ?>
                </select>
                <select name="class" id="classSelect">
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
                    <?php
                        foreach ($weekdays as $weekday) {
                            echo "<th>$weekday</th>";
                        }
                    ?>
                </tr>
                <?php 
                    foreach ($hours as $pos => $hour) {
                        echo "<tr>";
                        echo "<td value=" . $pos + 1 . ">$hour</td>";

                        for($i = 1; $i <= 6; $i++) {
                            echo "<td id=" . ($pos + 1) . $i .  ">";
                            foreach($schedule as $lesson) {
                                if ($lesson->hour == $pos + 1 && $lesson->weekday == $weekdays[$i -1]) {
                                    echo "<p>$lesson->teacher_email</p>";
                                    echo "<p>$lesson->class_year$lesson->class_section</p>";
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

    <script src="../javascripts/setTeachersSchedule.js"></script>
</body>

</html>

