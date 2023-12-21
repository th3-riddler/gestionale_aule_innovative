<?php

    require_once("db.php");

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        header("Location: ../index.php");
        exit();
    }

    if (!isset($_POST["email"]) || !isset($_POST["password"])) {
        header("Location: ../index.php");
        exit();
    }
    
    $email = $_POST["email"];
    $password = hash("sha256", $_POST["password"]);

    if($email == "" || $password == "") {
        header("Location: ../index.php");
    }

    $query = "SELECT * FROM docenti WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if(!empty($result)) {
        session_start();
        $_SESSION["email"] = $email;
        $_SESSION["nome"] = $result[0]["nome"];
        $_SESSION["cognome"] = $result[0]["cognome"];
        $_SESSION["materia"] = $result[0]["materia"];
        $_SESSION["sudo"] = false;
        header("Location: ../docenti/index.php");
        exit();
    }

    $query = "SELECT * FROM tecnici WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if(!empty($result)) {
        session_start();
        $_SESSION["email"] = $email;
        $_SESSION["nome"] = $result[0]["nome"];
        $_SESSION["cognome"] = $result[0]["cognome"];
        $_SESSION["sudo"] = true;
        header("Location: ../tecnici/index.php");
        exit();
    }

    header("Location: ../index.php");
?>