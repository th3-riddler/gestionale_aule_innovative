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
    <title>Home Tecnici</title>
</head>

<body>
    
    <div class="navbar alert m-4 w-auto">
        <div class="navbar-start">
            <a class="btn btn-ghost btn-active text-2xl mx-2">Benvenuto, <?php echo $_SESSION["surname"] . " " . $_SESSION["name"]; ?></a>
        </div>
        <div class="navbar-center">
            <ul class="menu menu-horizontal px-1">
                <li><a href="../tecnici/index.php" class="btn btn-ghost mx-2">Home</a></li>
                <li><a href="../tecnici/setTeachersSchedule.php" class="btn btn-ghost btn-active mx-2">Inserimento Orario</a></li>
                <li><a href="../tecnici/setCart.php" class="btn btn-ghost mx-2">Modifica Carrelli</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <button class="btn btn-ghost mx-2" onclick="modalHelp.showModal()">Guida</button>
            <a href="../API/logout.php" class="btn btn-error mx-2">Logout</a>
        </div>
    </div>

    <div class="card border bg-base-300 m-4">
        <div class="card-body">
            <button class="btn btn-ghost btn-active text-2xl mx-2">Aula <?php echo $_SESSION["current_room"]; ?></button>
            <table class="table mt-4">
                <tr class="hover">
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
                            $found = false;
                            foreach($schedule as $lesson) {
                                if ($lesson->hour == $pos + 1 && $lesson->weekday == $weekdays[$i -1]) {
                                    echo "<div class='carousel-item btn btn-wide btn-primary'>
                                        <h2 class='card-title'>" . $lesson->class_year . $lesson->class_section  . "</h2>
                                        <p>" . $lesson->teacher_email . "</p>
                                    </div>";
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                echo "<button class='carousel-item btn btn-wide btn-outline btn-disabled'><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12' /></svg></button>";
                            }
                            echo "</td>";
                        }

                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </div>

    <div class="alert flex m-4 w-auto" id="formTeacherSchedule">
        <h1 class="btn btn-ghost text-xl">Modifica l'Orario</h1>
        <form class="alert grow p-0" action="" method="post">
            <select class="select select-secondary w-full grow" onchange="this.form.submit()" name="room" id="roomSelect">
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
        <form class="alert grow p-0" id="formHour" action="../API/setTeachersSchedule.php" method="post">
            <input type="hidden" name="room" value="<?php echo $_SESSION["current_room"]; ?>">
            <select class="select select-bordered w-full grow" name="teacher" id="teacherSelect">
                <?php
                    foreach ($teachers as $teacher) {
                        echo "<option value='$teacher[1]'>$teacher[0]</option>";
                    }
                ?>
            </select>
            <select class="select select-bordered w-full grow" name="class" id="classSelect">
                <?php
                    foreach ($classes as $class) {
                        echo "<option value='$class'>$class</option>";
                    }
                ?>
            </select>
            <button id="submitter" class="btn btn-outline btn-success" type="submit" disabled>Aggiungi</button>
        </form>
    </div>

    <script src="../javascripts/setTeachersSchedule.js"></script>
</body>

</html>

