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

if (isset($_POST["cart"])) {
    $_SESSION["current_cart"] = $_POST["cart"];
    header("Location: setCart.php");
} else {
    if (!isset($_SESSION["current_cart"])) {
        $_SESSION["current_cart"] = 1;
    }
}

$token = $_COOKIE["token"];

$carts = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getCarts.php" . "?token=" . $token));
$cartsData = json_decode(file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/API/getCartsData.php" . "?token=" . $token));
?>

<!DOCTYPE html>
<html lang="it">

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

    <section id="main">
        <section id="insertion">
            <form id="formCart" action="../API/setCart.php" method="POST">
                <input type="hidden" name="current_cart" value="<?php echo $_SESSION["current_cart"] ?>">
                <input type="number" placeholder="N. massimo di pc" class="input input-bordered input-md w-full max-w-xs text-lg" min="0" step="1" name="pc_max" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->pc_max; ?>" id="inputPcMax" />
                <input type="text" name="Room1" id="aula1_input" class="input input-bordered input-md w-full max-w-xs text-lg" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room1; ?>" placeholder="Prima Aula"></input>
                <input type="text" name="Room2" id="aula2_input" class="input input-bordered input-md w-full max-w-xs text-lg" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room2; ?>" placeholder="Seconda Aula"></input>
                <input type="text" name="Room3" id="aula3_input" class="input input-bordered input-md w-full max-w-xs text-lg" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room3; ?>" placeholder="Terza Aula"></input>
                <input type="text" name="Room4" id="aula4_input" class="input input-bordered input-md w-full max-w-xs text-lg" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room4; ?>" placeholder="Quarta Aula"></input>
                <input type="text" name="Room5" id="aula5_input" class="input input-bordered input-md w-full max-w-xs text-lg" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room5; ?>" placeholder="Quinta Aula"></input>
                <input type="submit" value="Submit" class="btn" />
            </form>
        </section>
    </section>

    <div class="card border bg-base-300 m-4">
        <div class="card-body">
            <form action="" method="POST" class="btn btn-ghost btn-active text-2xl mx-2">

                <ul class="menu menu-horizontal bg-base-200 rounded-box">
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">1</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">2</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">3</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">4</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">5</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">6</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">7</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a type="radio" class="btn btn-ghost">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item">8</span>
                            </div>
                        </a>
                    </li>
                </ul>

                <!-- <select onchange="this.form.submit()" name="cart" id="cartSelect" class="select select-ghost select-active text-2xl mx-2">
                    <?php
                    /*foreach ($carts as $cart) {
                        if ($cart->id == $_SESSION["current_cart"]) {
                            echo "<option selected value='$cart->id'>$cart->cart_name</option>";
                            continue;
                        }
                        echo "<option value='$cart->id'>$cart->cart_name</option>";
                    }*/
                    ?>
                </select> -->
            </form>
            <table class="table mt-4">
                <tr class="hover">
                    <th>Nome Carrello</th>
                    <th>Numero massimo di pc</th>
                    <th>Aula 1</th>
                    <th>Aula 2</th>
                    <th>Aula 3</th>
                    <th>Aula 4</th>
                    <th>Aula 5</th>
                </tr>
                <?php
                foreach ($cartsData as $pos => $cart) {
                    echo "<tr>";
                    echo "<td>$cart->cart_name</td>";
                    echo "<td>$cart->pc_max</td>";
                    echo "<td>$cart->Room1</td>";
                    echo "<td>$cart->Room2</td>";
                    echo "<td>$cart->Room3</td>";
                    echo "<td>$cart->Room4</td>";
                    echo "<td>$cart->Room5</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>

</body>

</html>