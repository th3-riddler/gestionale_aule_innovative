<?php
session_start();

if(isset($_SESSION["email"])) {
    if($_SESSION["sudo"]) {
        header("Location: tecnici/index.php");
        exit();
    } else {
        header("Location: docenti/index.php");
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://rzmfx.github.io/site_styles/authentication.css">
    <link rel="stylesheet" href="css/auth.css">
    <title>Login</title>
    <script>
        let colors = ["#a6e3a1", "#fab387", "#eba0ac", "#cba6f7", "#f9e2af", "#89b4fa"];
        document.documentElement.style.setProperty('--accent', colors[Math.floor(Math.random() * colors.length)]);
    </script>
</head>
<body>
    <form action="API/auth.php" method="POST">
        <h1>Login</h1>
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
</body>
</html>