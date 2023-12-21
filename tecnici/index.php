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
    <title>Home Tecnici</title>
</head>
<body>
    <section id="userInfo">
        <h1>Benvenuto tecnico <?php echo $_SESSION["cognome"] . " " . $_SESSION["nome"] . "!" ?></h1>
        <a href="../API/logout.php">Logout</a>
    </section>
</body>
</html>