<?php
session_start();


if (!isset($_SESSION["email"])) {
    header("Location: ../index.php");
    exit();
}

if (!$_SESSION["sudo"]) {
    header("Location: ../docenti/teacherProfile.php");
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
    header("Location: technicianProfile.php");
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

$subjects = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getSubjects.php?token=" . $token), true);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Profilo</title>
</head>

<body style="zoom: 90%;">
    <div class="navbar alert m-4 w-auto">
        <div class="navbar-start">
            <div class="dropdown dropdown-hover">
                <div tabindex="0" role="button" class="avatar placeholder">
                    <div class="bg-neutral text-neutral-content rounded-full w-12 ml-3">
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
                    <li><a href="technicianProfile.php" class="btn-active">Profilo</a></li>
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
                <li><a href="../tecnici/setTeachersSchedule.php" class="btn btn-ghost mx-2">Inserimento Orario</a></li>
                <li><a href="../tecnici/setCart.php" class="btn btn-ghost mx-2">Modifica Carrelli</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <button class="btn btn-ghostm mx-2" onclick="modalHelp.showModal()">Guida</button>
        </div>
    </div>

    <div class="h-fit bg-base-200 alert flex w-auto card border bg-base-300 m-4">
        <div class="alert flex mb-4 w-full">
            <div class="avatar placeholder border-1">
                <div class="group bg-neutral w-36 rounded-full hover:bg-neutral/50 transition-all duration-200 relative">

                    <?php
                    $profileImage = getProfileImage('technician', $_SESSION['email']);

                    if ($profileImage != false) {
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer(base64_decode($profileImage));
                        echo '<img class="rounded-full absolute -z-2 top-0 bottom-0 right-0 left-0 w-full h-full group-hover:opacity-50" src="data:' . $mimeType . ';base64, ' . $profileImage . '" />';
                    } else {
                        echo '<span class="group-hover:opacity-50 text-5xl">' . $_SESSION["surname"][0] . $_SESSION["name"][0] . '</span>';
                    }
                    ?>
                    <svg id="camera" class="w-12 opacity-0 absolute group-hover:opacity-100 transition-opacity duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="#ffffff" d="M149.1 64.8L138.7 96H64C28.7 96 0 124.7 0 160V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64H373.3L362.9 64.8C356.4 45.2 338.1 32 317.4 32H194.6c-20.7 0-39 13.2-45.5 32.8zM256 192a96 96 0 1 1 0 192 96 96 0 1 1 0-192z" />
                    </svg>

                    <form action='<?php echo 'http://' . $_SERVER["SERVER_NAME"] . '/API/setProfileImage.php?work=technician&email=' . $_SESSION["email"] . '&token=' . $token ?>' method='POST' enctype='multipart/form-data' class="absolute top-0 bottom-0 right-0 left-0 w-full h-full">
                        <input type="file" name="profileImageSet" accept="image/jpeg,image/jpg,image/png,image/gif" class="hover:cursor-pointer rounded-full opacity-0 absolute top-0 bottom-0 right-0 left-0 w-full h-full" title="Cambia immagine profilo">
                    </form>

                </div>
            </div>
            <div class="flex flex-col ml-5">
                <h1 class="text-5xl font-bold"><?php echo $_SESSION["surname"] . " " . $_SESSION["name"]; ?></h1>
                <div class="flex flex-row w-fit items-center">
                    <p class="py-3"><?php echo $_SESSION["email"]; ?></p>
                    <button class="btn btn-sm btn-link text-primary no-underline" onclick="changePsw.showModal()">Cambia Password</button>
                </div>
                <p><?php echo $profileImage != false ? "<a href='http://" . $_SERVER["SERVER_NAME"] . "/API/deleteProfileImage.php?work=technician&email=" . $_SESSION["email"] . "&token=" . $token . "' class='bg-neutral btn btn-sm btn-ghost text-primary'>Rimuovi Immagine</a>" : '' ?></p>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="table">
                <!-- head -->
                <thead>
                    <tr>
                        <th>
                            <div class="flex flex-row justify-start gap-4 w-full">
                                <button class="btn btn-sm border-1 btn-square btn-outline btn-success text-xl" onclick="modalAddTeacher.showModal()">+</button>
                                <button id="delete" class="btn btn-sm border-1 btn-square btn-outline btn-error btn-disabled" onclick="confirmDelete.showModal()">
                                    <svg class="w-3 h-3" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <path fill="currentColor" d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z" />
                                    </svg>
                                </button>
                            </div>
                        </th>
                        <th>Nome</th>
                        <th>Materia</th>
                        <th>Statistiche</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    foreach ($teachers as $teacher) {
                    ?>
                        <tr>
                            <th>
                                <label>
                                    <input value="<?php echo $teacher['email'] ?>" type="checkbox" onchange="checkDeleteButton()" class="checkbox teacherCheckbox" />
                                </label>
                            </th>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder border-1">
                                        <div class="bg-neutral w-12 rounded-full">
                                            <?php
                                            $profileImage = getProfileImage('teacher', $teacher['email']);

                                            if ($profileImage != false) {
                                                $finfo = new finfo(FILEINFO_MIME_TYPE);
                                                $mimeType = $finfo->buffer(base64_decode($profileImage));
                                                echo '<img class="rounded-full relative -z-2 top-0 bottom-0 right-0 left-0 w-full h-full" src="data:' . $mimeType . ';base64, ' . $profileImage . '" />';
                                            } else {
                                                echo '<span class="text-xl">' . $teacher["surname"][0] . $teacher["name"][0] . '</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?php echo $teacher['surname'] . " " . $teacher['name']; ?></div>
                                        <div class="text-sm opacity-50"><?php echo $teacher['email']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="font-bold">
                                            <ul class="list-disc">
                                                <?php
                                                foreach ($teacher['subjects'] as $subject) {
                                                ?>
                                                    <li class="badge mb-1"><?php echo $subject; ?></li> <br>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-ghostm mx-2" onclick="openStatsModal('<?php echo $teacher['name'] . "', '" . $teacher['surname'] . "', '" . $teacher['email'] ?>')">Statistiche</button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <div class="toast">
            <div class="alert opacity-0 transition-opacity duration-700">
                <span>

                </span>
            </div>
        </div>
    </div>


    <dialog id="modalStats" class="modal">
        <div class="card w-max bg-base-300 p-8">
            <h1 class="font-bold text-lg text-center">Statistiche di ...</h1>
            <div class="stats bg-base-300 my-20">

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
                    <div class="stat-title">Page Views</div>
                    <div class="stat-value text-secondary">2.6M</div>
                    <div class="stat-desc mt-2">21% more than last month</div>
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

            <div class="modal-action">
                <form method="dialog">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn">Chiudi</button>
                </form>
            </div>
        </div>
    </dialog>

    <dialog id="changePsw" class="modal">
        <div class="modal-box">
            <h2 class="font-bold text-lg text-center">Cambia Password</h2>
            <p class="py-4 text-center">
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
                In quanto tecnico hai anche la possibilità di visualizzare una piccola anteprima dei docenti registrati al servizio. <br>
                Per ognuno di essi è anche possibile dare un'occhiata alle materie che insegna e alle <a class="text-secondary">statistiche</a> relative alle prenotazioni. <br><br>
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn">Chiudi</button>
                </form>
            </div>
        </div>
    </dialog>

    <dialog id="confirmDelete" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Vuoi davvero rimuovere?</h3>
            <p class="py-4">
                Sei sicuro di voler rimuovere i docenti selezionati? <br>
                Questa azione è irreversibile.
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-outline btn-primary" onclick="confirmDelete.close()">Annulla</button>
                    <button class="btn btn-outline btn-error" onclick="deleteTeachers()">Rimuovi</button>
                </form>
            </div>
        </div>
    </dialog>

    <dialog id="modalAddTeacher" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg">Aggiungi un docente</h3>

            <div id="nominativo" class="flex flex-row my-4 justify-center w-full gap-4 items-center flex-wrap">

                <label class="input input-bordered flex items-center gap-2 w-2/5 grow">
                    <input id="name" type="text" class="w-full" placeholder="Nome" required />
                </label>

                <label class="input input-bordered flex items-center gap-2 w-2/5 grow">
                    <input id="surname" type="text" class="w-full" placeholder="Cognome" required />
                </label>

                <label class="input input-bordered flex items-center gap-2 w-auto grow">
                    <input id="email" type="email" class="w-full" placeholder="Email" required />
                </label>

            </div>

            <h4 class="font-bold text-md mb-2">Seleziona Materie</h4> <!-- Materie -->
            <div id="materie" class="flex flex-row justify-around w-auto items-center gap-4 my-4">
                <select class="select select-bordered w-full grow" name="known-users" onchange="checkSelect()" required>
                    <option value="">--Nessuna Selezione--</option>
                    <?php
                    foreach ($subjects as $subject) {
                    ?>
                        <option value="<?php echo $subject['subject_name']; ?>"><?php echo $subject['subject_name']; ?></option>
                    <?php
                    }
                    ?>
                </select>
                <button class="btn btn-disabled" id="add">Add</button>
            </div>

            <ul id="subjects" class="menu bg-base-200 rounded-box my-1">
                <h2 class="menu-title">Materie Insegnate</h2>
            </ul>

            <button class="btn btn-outline btn-success mt-4" id="confirm">Confirm</button>
        </div>
    </dialog>

    <script src="../javascripts/technicianProfile.js"></script>
    <script>
        createAlert('<?php echo $errorMessages[$errorNumber] ?>', 'error')
    </script>
</body>

</html>