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
        <a href="insertion.php">Inserimento</a>
        <a href="insertionCart.php">Modifica Carrelli</a>
        <li><a id="logout" href="../API/logout.php">[ <-- </a></li>
    </section>

    <section id="main">
        <section id="date">
            <p></p>
        </section>
        <section id="filter">
            <form action="" method="POST">
                <input type="text" name="nome" placeholder="Nome">
                <input type="text" name="cognome" placeholder="Cognome">
                <input type="text" name="email" placeholder="Email">
                <input type="text" name="telefono" placeholder="Telefono">
                <input type="text" name="indirizzo" placeholder="Indirizzo">
                <input type="text" name="cap" placeholder="Cap">
                <input type="text" name="citta" placeholder="Città">
                <input type="text" name="provincia" placeholder="Provincia">
                <input type="text" name="stato" placeholder="Stato">
                <input type="submit" value="Cerca">
            </form>
        </section>
        <section id="schedule">
            <table>
                <tr>
                    <th>Lunedì</th>
                    <th>Martedì</th>
                    <th>Mercoledì</th>
                    <th>Giovedì</th>
                    <th>Venerdì</th>
                    <th>Sabato</th>
                </tr>
            </table>
        </section>
    </section>
</body>

</html>