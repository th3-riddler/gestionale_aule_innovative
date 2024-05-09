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
// If date is Sunday, set it to the next day (The API does not return the schedule for Sunday)
if (date('l', strtotime($date)) == "Sunday") {
    $date = date('Y-m-d', strtotime($date . ' + 1 days'));
}

$teacherSchedule = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeacherSchedule.php?email=" . $_SESSION["email"] . "&token=" . $token));

function getProfileImage($work, $email)
{
    global $token;
    $profileImage = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getProfileImage.php?work=" . $work . "&email=" . $email . "&token=" . $token), true);
    return $profileImage;
}

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
        <div class="navbar-start">

            <div class="dropdown dropdown-hover">
                <div tabindex="0" role="button" class="avatar placeholder">
                    <div class="avatar bg-neutral text-neutral-content rounded-full w-12 ml-3">
                        <?php $profileImage = getProfileImage('teacher', $_SESSION['email']);
                        echo $profileImage != false ? '<img class="absolute -z-2 top-0 bottom-0 right-0 left-0 w-full h-full group-hover:opacity-50" src="data:image/jpeg;base64, ' . $profileImage . '" />' : '<span class="group-hover:opacity-50 text-xl">' . $_SESSION["surname"][0] . $_SESSION["name"][0] . '</span>'; ?>
                    </div>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="teacherProfile.php">Profilo</a></li>
                    <li>
                        <details>
                            <summary>
                                Temi
                            </summary>
                            <ul>
                                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Dark" value="dark" /></li>
                                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Business" value="business" /></li>
                                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Night" value="night" /></li>
                                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Light" value="light" /></li>
                                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Nord" value="nord" /></li>
                                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Wireframe" value="wireframe" /></li>
                            </ul>
                        </details>
                    </li>
                    <li><a href="../API/logout.php" class="text-error">Logout</a></li>
                </ul>
            </div>

        </div>
        
        <div class="navbar-center">
            <ul class="menu menu-horizontal px-1">
                <li><a href="../docenti/index.php" class="btn btn-ghost btn-active mx-2">Home</a></li>
            </ul>
        </div>

        <div class="navbar-end">
            <button class="btn btn-ghostm mx-2" onclick="modalHelp.showModal()">Guida</button>
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

                    for ($i = 1; $i <= 6; $i++) {
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

                    $result = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeacherReservation.php?hour=" . $hour . "&date=" . $lessonDate . "&cart_id=" . $cart_id . "&room=" . $room . "&token=" . $token));
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
        <input class="input input-bordered w-1/6" name="pc_qt" id="inputPcQt" type="number" step="1" min="0" placeholder="Quantità di PC da prenotare" disabled required>
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
                Per prenotare un PC, clicca su una cella <a class="link link-primary link-hover">azzurra</a> (le celle <a class="link link-error link-hover">rosse</a> non hanno pc disponibili) e compila il form che apparirà. <br>
                Ricorda che non puoi prenotare più PC di quelli disponibili. <br><br>
                Se hai già prenotato dei PC, puoi vedere le informazioni relative alla tua prenotazione cliccando sulla cella. <br><br>
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