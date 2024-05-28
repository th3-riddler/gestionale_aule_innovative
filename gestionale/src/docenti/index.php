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

$errorMessages = [
    0 => "",
    1 => "Errore durante la prenotazione, riprova più tardi",
    2 => "Non puoi prenotare più PC di quelli disponibili",
    3 => "Non puoi prenotare un PC per un'ora passata",
    4 => "Non puoi eliminare una prenotazione per un'ora passata",
    5 => "Errore durante l'eliminazione della prenotazione, riprova più tardi",
    6 => "Prenotazione eliminata con successo",
    7 => "Non puoi prenotare un PC per una data superiore a un mese",
    8 => "Devi prenotare almeno un pc",
];

if (isset($_GET["error"])) {
    $_SESSION["error"] = $_GET["error"];
    header("Location: index.php" . (isset($_GET["date"]) ? "?date=" . $_GET["date"] : ""));
    exit();
}

$errorNumber = isset($_SESSION["error"]) ? $_SESSION["error"] : 0;
unset($_SESSION["error"]);

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

<body style="zoom: 90%;">

    <div class="navbar alert m-4 w-auto">
        <div class="navbar-start">

            <div class="dropdown dropdown-hover">
                <div tabindex="0" role="button" class="avatar placeholder">
                    <div class="avatar bg-neutral text-neutral-content rounded-full w-12 ml-3">
                        <?php
                        $profileImage = getProfileImage('teacher', $_SESSION['email']);

                        if ($profileImage != false) {
                            $finfo = new finfo(FILEINFO_MIME_TYPE);
                            $mimeType = $finfo->buffer(base64_decode($profileImage));
                            echo '<img class="rounded-full relative -z-2 top-0 bottom-0 right-0 left-0 w-full h-full" src="data:' . $mimeType . ';base64, ' . $profileImage . '" />';
                        } else {
                            echo '<span class="text-xl">' . $_SESSION["surname"][0] . $_SESSION["name"][0] . '</span>';
                        }
                        ?>
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
                <button class="join-item btn btn-outline" id="previous">Settimana Precedente</button>
                <div class="join-item btn btn-outline btn-active font-bold no-animation" id="current"></div>
                <button class="join-item btn btn-outline" id="next">Prossima Settimana</button>
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
        <input class="input input-bordered w-1/6" name="pc_qt" id="inputPcQt" type="number" step="1" min="1" placeholder="Quantità di PC da prenotare" disabled required>
        <label class="input input-bordered flex items-center gap-2 grow input-disabled">
            <input class="grow input-disabled" name="teacher_note" id="teacherNote" type="text" placeholder="Nota per il tecnico" disabled>
            <span class="badge badge-info">Opzionale</span>
        </label>
        <input type="hidden" name="teacher_email" value="<?php echo $_SESSION["email"] ?>">
        <button class="btn btn-outline btn-success" type="submit" disabled>Prenota</button>
    </form>

    <div class="toast -z-10">
        <div class="alert opacity-0 transition-opacity duration-700">
            <span>

            </span>
        </div>
    </div>

    <!-- <footer class="footer footer-center p-10 mt-10 pb-5">
        <aside class="items-center grid-flow-col">
            <svg width="50" height="50" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" class="fill-current">
                <path d="M22.672 15.226l-2.432.811.841 2.515c.33 1.019-.209 2.127-1.23 2.456-1.15.325-2.148-.321-2.463-1.226l-.84-2.518-5.013 1.677.84 2.517c.391 1.203-.434 2.542-1.831 2.542-.88 0-1.601-.564-1.86-1.314l-.842-2.516-2.431.809c-1.135.328-2.145-.317-2.463-1.229-.329-1.018.211-2.127 1.231-2.456l2.432-.809-1.621-4.823-2.432.808c-1.355.384-2.558-.59-2.558-1.839 0-.817.509-1.582 1.327-1.846l2.433-.809-.842-2.515c-.33-1.02.211-2.129 1.232-2.458 1.02-.329 2.13.209 2.461 1.229l.842 2.515 5.011-1.677-.839-2.517c-.403-1.238.484-2.553 1.843-2.553.819 0 1.585.509 1.85 1.326l.841 2.517 2.431-.81c1.02-.33 2.131.211 2.461 1.229.332 1.018-.21 2.126-1.23 2.456l-2.433.809 1.622 4.823 2.433-.809c1.242-.401 2.557.484 2.557 1.838 0 .819-.51 1.583-1.328 1.847m-8.992-6.428l-5.01 1.675 1.619 4.828 5.011-1.674-1.62-4.829z"></path>
            </svg>
            <p class="ml-4">RZMF © 2024 - All rights reserved</p>
        </aside>
        <nav>
            <div class="grid grid-flow-col gap-4">
                <a href="https://www.github.com/RZMFX">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 496 512" class="fill-current">
                        <path d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3 .3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5 .3-6.2 2.3zm44.2-1.7c-2.9 .7-4.9 2.6-4.6 4.9 .3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3 .7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3 .3 2.9 2.3 3.9 1.6 1 3.6 .7 4.3-.7 .7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3 .7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3 .7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z" />
                    </svg>
                </a>
                <a href="https://www.youtube.com/@rzmfx">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current">
                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path>
                    </svg>
                </a>
                <a href="https://www.instagram.com/rzmfx">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 448 512" class="fill-current">
                        <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
                    </svg>
                </a>
            </div>
        </nav>
    </footer> -->

    <dialog id="modalHelp" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Guida</h3>
            <p class="py-4">
                Questa pagina ti permette di prenotare dei PC per le tue lezioni. <br><br>
                Per prenotare un PC, clicca su una cella <a class="link link-primary link-hover">azzurra</a> (le celle <a class="link link-error link-hover">rosse</a> non hanno pc disponibili) e compila il form che apparirà. <br>
                Ricorda che non puoi prenotare più PC di quelli disponibili e li puoi prenotare solo dall'ora successiva a quella attuale. <br><br>
                Se hai già prenotato dei PC, puoi vedere le informazioni relative alla tua prenotazione cliccando sul tasto di informazioni sulla cella. <br><br>
                Se invece vuoi eliminare una prenotazione, clicca sul tasto di rimozione sulla cella. <br><br>
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

    <dialog id="modalDeleteReservation" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Vuoi davvero rimuovere?</h3>
            <p class="py-4">
                Sei sicuro di voler rimuovere le prenotazioni selezionate? <br>
                Questa azione è irreversibile.
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-outline btn-primary" onclick="modalDeleteReservation.close()">Annulla</button>
                    <button class="btn btn-outline btn-error" onclick="deleteReservation(event)">Rimuovi</button>
                </form>
            </div>
        </div>
    </dialog>

    <?php echo "<script>var scriptValues = " . json_encode($scriptValues) . ";</script>"; ?>
    <script src="../javascripts/indexTeachers.js"></script>
    <script>
        createAlert("<?php echo $errorMessages[$errorNumber] ?>", "<?php echo $errorNumber == 6 ? "success" : "error" ?>");
    </script>
</body>

</html>