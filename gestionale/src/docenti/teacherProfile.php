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

$token = $_COOKIE["token"];

$teachers = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getTeachers.php?token=" . $token), true);

function getProfileImage($work, $email)
{
    global $token;
    $profileImage = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getProfileImage.php?work=" . $work . "&email=" . $email . "&token=" . $token), true);
    return $profileImage;
}

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

<body>
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
                    <a href='http://" <?php echo $_SERVER["SERVER_NAME"] ?> "/API/changePassword.php?&email="<?php $_SESSION["email"] ?>"&token="<?php echo $token ?>"' class='btn btn-sm btn-link text-primary no-underline'>Cambia Password</a>
                </div>
                <p><?php echo $profileImage != false ? "<a href='http://" . $_SERVER["SERVER_NAME"] . "/API/deleteProfileImage.php?email=" . $_SESSION["email"] . "&token=" . $token . "' class='bg-neutral btn btn-sm btn-ghost text-primary'>Rimuovi Immagine</a>" : '' ?></p>
            </div>
        </div>

        <dialog id="modalHelp" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Guida</h3>
                <p class="py-4">
                    Questa pagina ti permette di visualizzare il tuo profilo personale. <br><br>
                    In questa sezione è anche possibile modificare la propria <a class="text-primary">immagine del profilo</a>, basta cliccare sulla tua icona attuale per selezionarne una nuova. <br>
                    In caso invece tu voglia <a class="text-error">rimuoverla</a>, ti basterà cliccare sul bottone <kbd class="kbd kbd-sm">Rimuovi Immagine</kbd>. <br><br>
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



        <script>
            function setThemeLocalStorage() {
                localStorage.setItem("theme", this.value);
            }

            if (localStorage.getItem("theme")) {
                // find the corresponding input radio and check it
                document
                    .querySelectorAll("input[name='theme']")
                    .forEach((theme) => (theme.checked = false));
                document.querySelector(
                    `input[value='${localStorage.getItem("theme")}']`
                ).checked = true;
            }

            document.querySelectorAll(".theme-controller").forEach((theme) => {
                theme.addEventListener("click", setThemeLocalStorage);
            });

            document.querySelector("input[type='file']").addEventListener("change", function() {
                this.form.submit();
            });
        </script>
</body>

</html>