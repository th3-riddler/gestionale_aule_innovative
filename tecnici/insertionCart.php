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
    header("Location: insertionCart.php");
} else {
    if (!isset($_SESSION["current_cart"])) {
        $_SESSION["current_cart"] = 1;
    }
}

$carts = json_decode(file_get_contents("http://127.0.0.1/esercizi_informatica/gestionale_CARRELLI/API/getCarts.php"));
$cartsData = json_decode(file_get_contents("http://127.0.0.1/esercizi_informatica/gestionale_CARRELLI/API/getCartsData.php"));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Home Tecnici</title>
</head>

<body>
    <section id="menu">
        <a href="../profile/profile.php">
            <li><?php echo $_SESSION["email"]; ?></li>
        </a>
        <li><?php echo $_SESSION["nome"]; ?></li>
        <li><?php echo $_SESSION["cognome"]; ?></li>
        <li><a id="logout" href="../API/logout.php">[ <-- </a></li>
    </section>

    <section id="main">
        <section id="cart_select">
            <form action="" method="POST">
                <select onchange="this.form.submit()" name="cart" id="cart_select">
                    <?php
                    foreach ($carts as $cart) {
                        if ($cart->id == $_SESSION["current_cart"]) {
                            echo "<option selected value='$cart->id'>$cart->nome_carrello</option>";
                            continue;
                        }
                        echo "<option value='$cart->id'>$cart->nome_carrello</option>";
                    }
                    ?>
                </select>
            </form>
        </section>
        <section id="insertion">
            <form id="form_cart" action="../API/insertionCart.php" method="POST">
                <input type="number" min="0" step="1" name="pc_max" id="pcmax_input" placeholder="N. massimo di pc"></input>
                <input type="number" min="0" step="1" name="pc_disp" id="pcdisp_input" placeholder="N. di pc disponibili"></input>
                <input type="text" name="Aula1" id="aula1_input" placeholder="Prima Aula"></input>
                <input type="text" name="Aula2" id="aula2_input" placeholder="Seconda Aula"></input>
                <input type="text" name="Aula3" id="aula3_input" placeholder="Terza Aula"></input>
                <input type="text" name="Aula4" id="aula4_input" placeholder="Quarta Aula"></input>
                <input type="text" name="Aula5" id="aula5_input" placeholder="Quinta Aula"></input>
                <button type="submit">Submit</button>
            </form>
        </section>
        <section id="schedule">
            <table>
                <tr>
                    <th>Nome Carrello</th>
                    <th>Numero massimo di pc</th>
                    <th>Numero di pc disponibili</th>
                    <th>Aula 1</th>
                    <th>Aula 2</th>
                    <th>Aula 3</th>
                    <th>Aula 4</th>
                    <th>Aula 5</th>
                </tr>
                <?php
                foreach ($cartsData as $pos => $cart) {
                    echo "<tr>";
                    echo "<td>$cart->nome_carrello</td>";
                    echo "<td>$cart->pc_max</td>";
                    echo "<td>$cart->pc_disp</td>";
                    echo "<td>$cart->Aula1</td>";
                    echo "<td>$cart->Aula2</td>";
                    echo "<td>$cart->Aula3</td>";
                    echo "<td>$cart->Aula4</td>";
                    echo "<td>$cart->Aula5</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </section>
    </section>

</body>

</html>