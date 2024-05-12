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

$rooms = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getRooms.php" . "?token=" . $token));
$current_room = $_GET["current_room"] ?? "A1";
if (!in_array($current_room, $rooms)) {
    $current_room = "A1";
}

$teachers = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeachers.php" . "?token=" . $token));
$classes = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getClasses.php" . "?token=" . $token));
$schedule = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getSchedule.php?room=" . $current_room . "&token=" . $token));

$hours = ["8:10 - 9:10", "9:10 - 10:00", "10:10 - 11:10", "11:10 - 12:00", "12:10 - 13:10", "13:10 - 14:05", "14:20 - 15:10", "15:10 - 16:10"];
$weekdays = ["Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"];

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
    <title>Home Tecnici</title>
</head>

<body>

    <div class="navbar alert m-4 w-auto">
        <div class="navbar-start">
            <div class="dropdown dropdown-hover">
                <div tabindex="0" role="button" class="avatar placeholder">
                    <div class="avatar bg-neutral text-neutral-content rounded-full w-12 ml-3">
                        <?php
                        $profileImage = getProfileImage('technician', $_SESSION['email']);

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
                    <li><a href="technicianProfile.php">Profilo</a></li>
                    <li>
                        <details>
                            <summary>Temi</summary>
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
                <li><a href="../tecnici/index.php" class="btn btn-ghost mx-2">Home</a></li>
                <li><a href="../tecnici/setTeachersSchedule.php" class="btn btn-ghost btn-active mx-2">Inserimento Orario</a></li>
                <li><a href="../tecnici/setCart.php" class="btn btn-ghost mx-2">Modifica Carrelli</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <button class="btn btn-ghost mx-2" onclick="modalHelp.showModal()">Guida</button>
        </div>
    </div>

    <div class="card border bg-base-300 m-4">
        <div class="card-body">
            <div role="tablist" class="tabs tabs-boxed">
                <?php
                foreach ($rooms as $room) {
                    echo "<a href='setTeachersSchedule.php?current_room=$room' class='tab" . ($room == $current_room ? " tab-active" : "") . " px-2'>" . $room . "</a>";
                }
                ?>
            </div>
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

                    for ($i = 1; $i <= 6; $i++) {
                        echo "<td id=" . ($pos + 1) . $i .  ">";
                        $found = false;
                        foreach ($schedule as $lesson) {
                            if ($lesson->hour == $pos + 1 && $lesson->weekday == $weekdays[$i - 1]) {
                                echo "<div class='btn btn-wide bg-primary text-black hover:bg-primary/50 transition-all duration-200'>
                                        <h2 class='card-title'>" . $lesson->class_year . $lesson->class_section  . "</h2>
                                        <p class='text-xs'>" . $lesson->teacher_email . "</p>
                                    </div>";
                                $found = true;
                            }
                        }
                        if (!$found) {
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

    <div class="alert flex m-4 w-auto" id="formTeacherSchedule">
        <h1 class="btn btn-ghost text-xl">Modifica l'Orario</h1>

        <form class="alert grow p-0" id="formHour" action="../API/setTeachersSchedule.php" method="post">
            <input type="hidden" name="room" value="<?php echo $current_room; ?>" />
            <select class="select select-bordered w-full grow" name="teacher" id="teacherSelect">
                <?php
                foreach ($teachers as $teacher) {
                    echo "<option value='$teacher->email'>$teacher->surname $teacher->name</option>";
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

    <dialog id="modalHelp" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Guida</h3>
            <p class="py-4">
                Questa pagina ti permette di gestire l'Orario dei Docenti. <br><br>
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn">Chiudi</button>
                </form>
            </div>
        </div>
    </dialog>

    <script src="../javascripts/setTeachersSchedule.js"></script>
</body>

</html>