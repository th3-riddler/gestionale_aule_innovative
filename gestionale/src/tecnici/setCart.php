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
            <a class="btn btn-ghost btn-active text-2xl mx-2">Benvenuto, <?php echo $_SESSION["surname"] . " " . $_SESSION["name"]; ?></a>
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
            <a href="../API/logout.php" class="btn btn-error mx-2">Logout</a>
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
                            echo "<td><input type='text' name='$key' value='$value' class='input input-bordered input-primary btn-wide text-center'></td>";
                        } else {
                            echo "<td><div class='btn btn-wide btn-outline btn-disabled'>$value</div></td>";
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

</body>

</html>