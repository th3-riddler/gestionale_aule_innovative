<?php
session_start();


if (!isset($_SESSION["email"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION["sudo"]) {
    header("Location: ../tecnici/technicianProfile.php");
    exit();
}

$errorMessages = [
    0 => "",
    1 => "Il file è troppo grande, massimo 64KB",
    2 => "Il file ha un formato non valido, accettati solo JPG, PNG e GIF",
    3 => "Errore nel caricamento del file"
];

if (isset($_GET["error"])) {
    $_SESSION["error"] = $_GET["error"];
    header("Location: teacherProfile.php");
    exit();
}

$errorNumber = isset($_SESSION["error"]) ? $_SESSION["error"] : 0;
unset($_SESSION["error"]);

$token = $_COOKIE["token"];

$teachers = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeachers.php?token=" . $token), true);

function getProfileImage($work, $email)
{
    global $token;
    $profileImage = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getProfileImage.php?work=" . $work . "&email=" . $email . "&token=" . $token), true);
    return $profileImage;
}

$stats = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getStats.php?email=" . $_SESSION["email"] . "&token=" . $token), true);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Profilo Personale</title>
</head>

<body style="zoom: 90%;">
    <div class="navbar alert m-4 w-auto">
        <div class="navbar-start">
            <div class="dropdown dropdown-hover">
                <div tabindex="0" role="button" class="avatar placeholder">
                    <div class="bg-neutral text-neutral-content rounded-full w-12 ml-3">
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
                    <li><a href="teacherProfile.php" class="btn-active">Profilo</a></li>
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
                <li><a href="../docenti/index.php" class="btn btn-ghost mx-2">Home</a></li>
            </ul>
        </div>

        <div class="navbar-end">
            <button class="btn btn-ghostm mx-2" onclick="modalHelp.showModal()">Guida</button>
        </div>
    </div>

    <div class="h-fit alert flex w-auto card border bg-base-300 m-4">
        <div class="alert flex mb-4 w-full">
            <div class="avatar placeholder border-1">
                <div class="group bg-neutral w-36 rounded-full hover:bg-neutral/50 transition-all duration-200 relative">

                    <?php
                    $profileImage = getProfileImage('teacher', $_SESSION['email']);

                    if ($profileImage != false) {
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer(base64_decode($profileImage));
                        echo '<img class="rounded-full absolute -z-2 top-0 bottom-0 right-0 left-0 w-full h-full group-hover:opacity-50" src="data:' . $mimeType . ';base64, ' . $profileImage . '" />';
                    } else {
                        echo '<span class="group-hover:opacity-50 text-5xl">' . $_SESSION["surname"][0] . $_SESSION["name"][0] . '</span>';
                    }
                    ?>
                    <svg id="camera" class="w-12 opacity-0 absolute group-hover:opacity-100 transition-opacity duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                        <path fill="#ffffff" d="M149.1 64.8L138.7 96H64C28.7 96 0 124.7 0 160V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64H373.3L362.9 64.8C356.4 45.2 338.1 32 317.4 32H194.6c-20.7 0-39 13.2-45.5 32.8zM256 192a96 96 0 1 1 0 192 96 96 0 1 1 0-192z" />
                    </svg>

                    <form action='<?php echo 'http://' . $_SERVER["SERVER_NAME"] . '/API/setProfileImage.php?work=teacher&email=' . $_SESSION["email"] . '&token=' . $token ?>' method='POST' enctype='multipart/form-data' class="absolute top-0 bottom-0 right-0 left-0 w-full h-full">
                        <input type="file" name="profileImageSet" accept="image/jpeg" class="hover:cursor-pointer rounded-full opacity-0 absolute top-0 bottom-0 right-0 left-0 w-full h-full" title="Cambia immagine profilo">
                    </form>

                </div>
            </div>
            <div class="flex flex-col ml-5">
                <h1 class="text-5xl font-bold"><?php echo $_SESSION["surname"] . " " . $_SESSION["name"]; ?></h1>
                <div class="flex flex-row w-fit items-center">
                    <p class="py-3"><?php echo $_SESSION["email"]; ?></p>
                    <button class="btn btn-sm btn-link text-primary no-underline" onclick="changePsw.showModal()">Cambia Password</button>
                </div>
                <p><?php echo $profileImage != false ? "<a href='http://" . $_SERVER["SERVER_NAME"] . "/API/deleteProfileImage.php?email=" . $_SESSION["email"] . "&token=" . $token . "' class='bg-neutral btn btn-sm btn-ghost text-primary'>Rimuovi Immagine</a>" : '' ?></p>
            </div>
        </div>

        <div class="stats bg-base-300 my-20 w-11/12">

            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="inline-block w-8 h-8 stroke-current" viewBox="0 0 640 512">
                        <path fill="currentColor" d="M384 96V320H64L64 96H384zM64 32C28.7 32 0 60.7 0 96V320c0 35.3 28.7 64 64 64H181.3l-10.7 32H96c-17.7 0-32 14.3-32 32s14.3 32 32 32H352c17.7 0 32-14.3 32-32s-14.3-32-32-32H277.3l-10.7-32H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm464 0c-26.5 0-48 21.5-48 48V432c0 26.5 21.5 48 48 48h64c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H528zm16 64h32c8.8 0 16 7.2 16 16s-7.2 16-16 16H544c-8.8 0-16-7.2-16-16s7.2-16 16-16zm-16 80c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16s-7.2 16-16 16H544c-8.8 0-16-7.2-16-16zm32 160a32 32 0 1 1 0 64 32 32 0 1 1 0-64z" />
                    </svg>
                </div>
                <div class="stat-title font-bold text-xl w-fit">Numero Totale di PC prenotati</div>
                <div class="stat-value text-primary w-fit"><?php echo $stats["pc"]["teacher_pc"] ?? 0 ?></div>
                <div class="stat-desc text-base mt-2 w-fit">Percentuale di PC prenotati rispetto al totale: <span class="text-info font-bold"><?php echo $stats["pc"]["percentage"] ?? 0 ?>%</span></div>
            </div>

            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="stat-title font-bold text-xl w-fit">Numero Totale di Prenotazioni</div>
                <div class="stat-value text-base-500 w-fit"><?php echo $stats["reservation"]["teacher_reservation"] ?? 0 ?></div>
                <div class="stat-desc text-base mt-2 w-fit">Percentuale di prenotazioni rispetto al totale: <span class="text-info font-bold"><?php echo $stats["reservation"]["percentage"] ?? 0 ?>%</div>
            </div>

            <div class="stat">
                <div class="stat-title font-bold text-xl w-fit">Prenotazioni soddisfatte</div>
                <div class="stat-value text-secondary w-fit"><?php echo $stats["completed"]["percentage"] ?? 0 ?>%</div>
                <div class="stat-desc text-base w-fit">Numero prenotazioni rimanenti: <span class="text-info font-bold"><?php echo $stats["completed"]["uncompleted"] ?? 0 ?></span></div>
            </div>

        </div>

        <div class="toast">
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

    </div>


    <dialog id="changePsw" class="modal">
        <div class="modal-box">
            <h2 class="font-bold text-lg text-center">Cambia Password</h2>
            <p class="py-4">
                La password deve essere lunga almeno 8 caratteri.
            </p>

            <div id="currentPsw" class="flex flex-row my-4 justify-between w-full gap-4 items-center flex-wrap">

                <!-- PASSWORD CORRENTE -->
                <div class="flex flex-col w-full">
                    <div class="label">
                        <span class="label-text font-bold">Password Corrente</span>
                    </div>
                    <label id="currentPswLabel" class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                            <path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" />
                        </svg>
                        <input id="passwordCorrente" type="password" class="grow" required />
                    </label>
                    <div class="label">
                        <span class="absolute mt-2 alertPsw label-text-alt text-red-400 hidden"></span>
                    </div>
                </div>

                <!-- NUOVA PASSWORD -->
                <div class="flex flex-col w-[47.5%]">
                    <div class="label">
                        <span class="label-text font-bold">Nuova Password</span>
                    </div>
                    <label id="newPswLabel" class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                            <path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" />
                        </svg>
                        <input id="nuovaPassword" type="password" class="grow w-1/2" required />
                    </label>
                    <div class="label">
                        <span class="absolute mt-2 alertPsw label-text-alt text-red-400 hidden"></span>
                    </div>
                </div>

                <!-- CONFERMA PASSWORD -->
                <div class="flex flex-col w-[47.5%]">
                    <div class="label">
                        <span class="label-text font-bold">Conferma Password</span>
                    </div>
                    <label id="confirmPswLabel" class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                            <path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" />
                        </svg>
                        <input id="confermaPassword" type="password" class="grow w-1/2" required />
                    </label>
                    <div class="label">
                        <span class="alertPsw absolute mt-2 label-text-alt text-red-400 hidden"></span>
                    </div>
                </div>

            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-outline btn-primary" onclick="changePsw.close()">Annulla</button>
                    <button class="btn btn-outline btn-success" onclick="changePassword(event)">Cambia</button>
                    <input type="hidden" name="technicianEmail" value="<?php echo $_SESSION['email'] ?>">
                </form>
            </div>
        </div>
    </dialog>


    <dialog id="modalHelp" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Guida</h3>
            <p class="py-4">
                Questa pagina ti permette di visualizzare il tuo profilo personale. <br><br>
                In questa sezione è anche possibile modificare la propria <a class="text-primary">immagine del profilo</a>, basta cliccare sulla tua icona attuale per selezionarne una nuova. <br>
                In caso invece tu voglia <a class="text-error">rimuoverla</a>, ti basterà cliccare sul bottone <kbd class="kbd kbd-sm">Rimuovi Immagine</kbd>. <br><br>
                A fianco al tuo indirizzo email è presente un bottone <kbd class="kbd kbd-sm">Cambia Password</kbd> in caso tu voglia cambiarla. <br><br>
                Nella parte sottostante è possibile dare un'occhiata a delle <a class="text-secondary">statistiche</a> generali relative alle tue prenotazioni. <br><br>
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn">Chiudi</button>
                </form>
            </div>
        </div>
    </dialog>



    <script src="../javascripts/teacherProfile.js"></script>
    <script>
        createAlert("<?php echo $errorMessages[$errorNumber] ?>", "error")
    </script>
</body>

</html>