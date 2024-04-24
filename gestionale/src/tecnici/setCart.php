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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Home Tecnici</title>
</head>

<body>
    <section id="menu">
        <a href="../profile/profile.php">
            <li><?php echo $_SESSION["email"]; ?></li>
        </a>
        <li><?php echo $_SESSION["name"]; ?></li>
        <li><?php echo $_SESSION["surname"]; ?></li>
        <li><a id="logout" href="../API/logout.php">[ <-- </a></li>
    </section>

    <section id="main">
        <section id="cartSelectSectiom">
            <form action="" method="POST">
                <select onchange="this.form.submit()" name="cart" id="cartSelect">
                    <?php
                    foreach ($carts as $cart) {
                        if ($cart->id == $_SESSION["current_cart"]) {
                            echo "<option selected value='$cart->id'>$cart->cart_name</option>";
                            continue;
                        }
                        echo "<option value='$cart->id'>$cart->cart_name</option>";
                    }
                    ?>
                </select>
            </form>
        </section>
        <section id="insertion">
            <form id="formCart" action="../API/setCart.php" method="POST">
                <input type="hidden" name="current_cart" value="<?php echo $_SESSION["current_cart"] ?>">
                <input type="number" min="0" step="1" name="pc_max" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->pc_max; ?>" id="inputPcMax" placeholder="N. massimo di pc"></input>
                <input type="text" name="Room1" id="aula1_input" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room1; ?>" placeholder="Prima Aula"></input>
                <input type="text" name="Room2" id="aula2_input" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room2; ?>" placeholder="Seconda Aula"></input>
                <input type="text" name="Room3" id="aula3_input" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room3; ?>" placeholder="Terza Aula"></input>
                <input type="text" name="Room4" id="aula4_input" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room4; ?>" placeholder="Quarta Aula"></input>
                <input type="text" name="Room5" id="aula5_input" value="<?php echo $cartsData[$_SESSION["current_cart"] - 1]->Room5; ?>" placeholder="Quinta Aula"></input>
                <button type="submit">Submit</button>
            </form>
        </section>
        <section id="schedule">
            <table>
                <tr>
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
        </section>
    </section>

</body>

</html>