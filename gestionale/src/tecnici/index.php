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

$date = $_GET["date"] ?? date("Y-m-d");

$week_reservations = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getWeekReservations.php?date=" . $date . "&token=" . $token), true);
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
                <li><a href="../tecnici/index.php" class="btn btn-ghost btn-active mx-2">Home</a></li>
                <li><a href="../tecnici/setTeachersSchedule.php" class="btn btn-ghost mx-2">Inserimento Orario</a></li>
                <li><a href="../tecnici/setCart.php" class="btn btn-ghost mx-2">Modifica Carrelli</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <button class="btn btn-ghostm mx-2" onclick="modalHelp.showModal()">Guida</button>
            <a href="../API/logout.php" class="btn btn-error mx-2">Logout</a>
        </div>
    </div>

    <div class="card border bg-base-300 m-4">
        <div class="card-body">
            <div class="join grid grid-cols-3" id="dateSection">
                <button class="join-item btn btn-outline" id="previous">Previous Week</button>
                <div class="join-item btn btn-outline btn-active font-bold no-animation" id="current"></div>
                <button class="join-item btn btn-outline" id="next">Next Week</button>
            </div>

            <table class="table mt-4">
                <tr class="hover">
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
                            $counter = 0;
                            foreach ($week_reservations as $reservation) {
                                if ($reservation["hour"] == $pos + 1 && $reservation["weekday"] == $weekdays_it[$i - 1]) {
                                    echo "<div id='" . ($pos + 1) . $i ."rev" . ($counter + 1) . "' class='btn btn-wide btn-" . ($reservation["technician_note"] ? "secondary" : "primary") . " reservation'" . ($reservation["technician_note"] ? "onclick='modalTeacherNote.showModal()'" : "") . ">
                                        <h2 class='card-title'><span class='room'>" . $reservation["room"]  . "</span></h2>
                                        <p>PC prenotati: <span class='pc'>" . $reservation["pc_qt"] . "</span></p>
                                        <input type='hidden' name='teacherNote' value='" . $reservation["teacher_note"] . "'>
                                    </div>";
                                    $counter++;
                                }
                            }

                            if ($counter == 0) {
                                echo "<button class='btn btn-wide btn-outline btn-disabled'><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12' /></svg></button>";
                            }

                            echo "</td>";
                        }

                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </div>

    <form class="alert flex m-4 w-auto" action="../API/setTechnicianNote.php" method="POST" id="formReservation">
        <h1 class="btn btn-ghost text-xl">Aggiungi una nota alla prenotazione</h1>
        <input class="input input-bordered grow" name="technician_note" id="technicianNote" type="text" placeholder="Nota per il docente" disabled required>
        <button id="teacherNote" class="btn btn-ghost mx-2 hidden" onclick="modalTeacherNote.showModal()">Nota per Te</button>
        <button class="btn btn-outline btn-success" type="submit" disabled>Annota</button>
    </form>

    <dialog id="modalTeacherNote" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Nota del Docente</h3>
            <p class="py-4"></p>
            <div class="modal-action">
            <form method="dialog">
                <!-- if there is a button in form, it will close the modal -->
                <button class="btn">Chiudi</button>
            </form>
            </div>
        </div>
    </dialog>

    <dialog id="modalHelp" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Guida</h3>
            <p class="py-4">
                Questa pagina ti permette di gestire le Prenotazioni dei Docenti. <br><br>
            </p>
            <div class="modal-action">
            <form method="dialog">
                <!-- if there is a button in form, it will close the modal -->
                <button class="btn">Chiudi</button>
            </form>
            </div>
        </div>
    </dialog>

    <script src="../javascripts/indexTechnician.js"></script>
</body>
</html>