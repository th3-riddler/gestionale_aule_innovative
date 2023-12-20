<head>
<title>Home Docenti</title>
</head>
<?php
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: ../login.html");
    exit();
}

if ($_SESSION["sudo"] == true) {
    header("Location: ../tecnici/index.php");
    exit();
}

echo "Benvenuto docente " . $_SESSION["nome"] . "!";
?>