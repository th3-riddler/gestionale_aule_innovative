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

$errorNumber = $_SESSION["error"] ?? 0;

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
                    <li><a href="teacherProfile.php" class="bg-neutral">Profilo</a></li>
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

        <div class="stats shadow bg-base-300 my-20 w-11/12">

            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="stat-title">Numero Totale di PC prenotati</div>
                <div class="stat-value text-primary"><?php echo $stats[0]["pc_tot"] ?></div>
                <div class="stat-desc">21% more than last month</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="stat-title">Page Views</div>
                <div class="stat-value text-secondary">2.6M</div>
                <div class="stat-desc">21% more than last month</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-secondary">
                    <div class="avatar online">
                        <div class="w-16 rounded-full">
                            <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg" />
                        </div>
                    </div>
                </div>
                <div class="stat-value">86%</div>
                <div class="stat-title">Tasks done</div>
                <div class="stat-desc text-secondary">31 tasks remaining</div>
            </div>

        </div>
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
        createAlert('<?php echo $errorMessages[$errorNumber] ?>', 'error')
    </script>
</body>

</html>