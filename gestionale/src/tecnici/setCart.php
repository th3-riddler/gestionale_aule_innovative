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

$current_cart = intval($_GET["current_cart"] ?? 1);
if ($current_cart < 1 || $current_cart > 8) {
    $current_cart = 1;
}

$token = $_COOKIE["token"];

$carts = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getCarts.php" . "?token=" . $token));
$cartsData = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getCartsData.php" . "?token=" . $token));

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
                        <?php $profileImage = getProfileImage('technician', $_SESSION['email']);
                        echo $profileImage != false ? '<img class="absolute -z-2 top-0 bottom-0 right-0 left-0 w-full h-full group-hover:opacity-50" src="data:image/jpeg;base64, ' . $profileImage . '" />' : '<span class="group-hover:opacity-50 text-xl">' . $_SESSION["surname"][0] . $_SESSION["name"][0] . '</span>'; ?>
                    </div>

                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="technicianProfile.php">Profilo</a></li>
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
                <li><a href="../tecnici/index.php" class="btn btn-ghost mx-2">Home</a></li>
                <li><a href="../tecnici/setTeachersSchedule.php" class="btn btn-ghost mx-2">Inserimento Orario</a></li>
                <li><a href="../tecnici/setCart.php" class="btn btn-ghost btn-active mx-2">Modifica Carrelli</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <button class="btn btn-ghost mx-2" onclick="modalHelp.showModal()">Guida</button>
        </div>
    </div>

    <div class="card border bg-base-300 m-4">
        <div class="card-body">
            <ul class="menu menu-horizontal bg-base-200 rounded-box justify-center">
                <?php
                for ($i = 1; $i < 9; $i++) {
                    echo "<li>
                        <a href=\"../tecnici/setCart.php?current_cart=$i\" class=\"btn btn-ghost mx-2" . ($i == $current_cart ? " btn-active" : "") . "\">
                            <div class=\"indicator\">
                                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z\" />
                                </svg>
                                <span class=\"badge badge-sm indicator-item\">$i</span>
                            </div>
                        </a>
                    </li>";
                }
                ?>
            </ul>

            <table class="table mt-4">
                <tr class="hover">
                    <th>Nome Carrello</th>
                    <th>Numero massimo di pc</th>
                    <th>Aula 1</th>
                    <th>Aula 2</th>
                    <th>Aula 3</th>
                    <th>Aula 4</th>
                    <th>Aula 5</th>
                    <th></th>
                </tr>
                <?php
                foreach ($cartsData as $pos => $cart) {
                    echo "<tr>";

                    if ($pos + 1 == $current_cart) echo "<form id='formCart' action='../API/setCart.php' method='POST'><input type='hidden' name='current_cart' value='$current_cart'>";

                    foreach ($cart as $key => $value) {
                        if ($key == "id") continue;
                        if ($pos + 1 == $current_cart) {
                            echo "<td><input type='text' name='$key' value='$value' class='input input-bordered input-primary btn-square w-40 text-center'></td>";
                        } else {
                            echo "<td><div class='btn btn-square w-40 btn-outline btn-disabled'>$value</div></td>";
                        }
                    }

                    if ($pos + 1 == $current_cart) {
                        echo "<td><button type='submit' title='Confirm' class='btn btn-square btn-outline'><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path fill='currentColor' d='M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z'/></svg></button></td></form>";
                    } else {
                        echo "<td><button class='btn btn-square btn-outline btn-disabled'><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12' /></svg></button></td>";
                    }

                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <dialog id="modalHelp" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Guida</h3>
            <p class="py-4">
                Questa pagina ti permette di gestire i Carrelli. <br><br>
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
    </script>

</body>

</html>