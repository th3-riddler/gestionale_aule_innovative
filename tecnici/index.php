<head>
<title>Home Tecnici</title>
</head>
<?php
session_start();


if (!isset($_SESSION["email"])) {
    header("Location: ../login.html");
    exit();
}

if ($_SESSION["sudo"] == false) {
    header("Location: ../docenti/index.php");
    exit();
}

echo "Benvenuto tecnico " . $_SESSION["nome"] . "!";
?>