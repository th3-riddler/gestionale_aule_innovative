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
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
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