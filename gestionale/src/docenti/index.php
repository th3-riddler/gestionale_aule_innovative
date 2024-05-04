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
<html lang="it" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Home Docenti</title>
</head>
<body>

    <div class="navbar alert m-4 w-auto">
        <div class="flex-1">
            <a class="btn btn-ghost btn-active text-2xl">Benvenuto, <?php echo $_SESSION["surname"] . " " . $_SESSION["name"]; ?></a>
        </div>
        <div class="flex-none">
            <button class="btn btn-ghost mr-4" onclick="modalHelp.showModal()">Guida</button>
            <a href="../API/logout.php" class="btn btn-error">Logout</a>
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
                        echo "<tr class='hover'><td value=" . $pos + 1 . ">$hour</td>";

                        for($i = 1; $i <= 6; $i++) {
                            echo "<td id=" . ($pos + 1) . $i .  ">";
                            echo "<button class='btn btn-wide btn-outline btn-disabled'><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12' /></svg></button>";
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
                        $final_note = ($result->technician_note) ?? "Nessuna nota presente!";
                        $had_reservation = (empty($result)) ? false : true;

                        $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeacherReservation.php?hour=" . $hour . "&date=" . $lessonDate . "&cart_id=" . $cart_id . "&token=" . $token));
                        $final_pc_reserved = $result->pc_qt ?? 0;

                        // Add the values to the object that will be used in the script
                        $scriptValues[] = ["hour" => $hour, "weekdayNumber" => $weekdayNumber, "class" => $class, "section" => $section, "room" => $room, "final_pc_number" => $final_pc_number, "final_note" => $final_note, "cart_id" => $cart_id, "had_reservation" => $had_reservation, "final_pc_reserved" => $final_pc_reserved];
                    }
                ?>
            </table>
        </div>
    </div>

    <form class="alert flex m-4 w-auto" action="../API/setReservation.php" method="POST" id="formReservation">
        <h1 class="btn btn-ghost text-xl">Prenota dei PC</h1>
        <input class="input input-bordered w-1/6" name="pc_qt" id="inputPcQt" type="number" step = "1" min = "0" placeholder="Quantitá di PC da prenotare" disabled required>
        <label class="input input-bordered flex items-center gap-2 grow input-disabled">
            <input class="grow input-disabled" name="teacher_note" id="teacherNote" type="text" placeholder="Nota per il tecnico" disabled>
            <span class="badge badge-info">Opzionale</span>
        </label>
        <button class="btn btn-outline btn-success" type="submit" disabled>Prenota</button>
    </form>

    <dialog id="modalHelp" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Guida</h3>
            <p class="py-4">
                Questa pagina ti permette di prenotare dei PC per le tue lezioni. <br><br>
                Per prenotare un PC, clicca su una cella <a class="link link-primary link-hover">azzurra</a> (le celle <a class="link link-error link-hover">rosse</a> non hanno pc disponibili) e compila il form che apparirá. <br>
                Ricorda che non puoi prenotare piú PC di quelli disponibili. <br><br>
                Se hai giá prenotato dei PC, puoi vedere le informazioni relative alla tua prenotazione cliccando sulla cella. <br><br>
                Se hai bisogno di aiuto, clicca sul bottone <kbd class="kbd kbd-sm">Guida</kbd> in alto a destra o contatta un tecnico.
            </p>
            <div class="modal-action">
            <form method="dialog">
                <!-- if there is a button in form, it will close the modal -->
                <button class="btn">Chiudi</button>
            </form>
            </div>
        </div>
    </dialog>

    <?php echo "<script>var scriptValues = " . json_encode($scriptValues) . ";</script>"; ?>
    <script src="../javascripts/indexTeachers.js"></script>
</body>
</html>
