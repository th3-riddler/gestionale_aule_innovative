<?php
header("Content-Type: application/json");
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

function generateUniqueToken($existingTokens, $email, $type = "teacher") {
    global $conn;
    $token = bin2hex(random_bytes(32)); 
    while (in_array($token, $existingTokens)) {
        $token = bin2hex(random_bytes(32));
    }
    $query = "INSERT INTO api_" . $type .  "token (email, token) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    return $token;
}

$query = "SELECT * FROM teacher WHERE email = ? AND password = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if(!empty($result)) {
    session_start();

    // get all the tokens
    $query = "SELECT * FROM api_teachertoken";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultalltokens = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $alltokens = array_column($resultalltokens, "token");

    // check if in api_teachertoken there is a token for this user that is still valid (not expired)
    $query = "SELECT * FROM api_teachertoken WHERE email = ? AND date_created > DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultpresenttoken = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // if there is a valid token, use it
    $resultpresenttoken ? $token = $resultpresenttoken[0]["token"] : $token = generateUniqueToken($alltokens, $email);
    
    $_SESSION["email"] = $email;
    $_SESSION["name"] = $result[0]["name"];
    $_SESSION["surname"] = $result[0]["surname"];

    setcookie("token", $token, time() + 86400, "/");
    $_SESSION["sudo"] = false;

    header("Location: ../docenti/index.php");
    exit();
}

$query = "SELECT * FROM technician WHERE email = ? AND password = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if(!empty($result)) {
    session_start();

    // get all the tokens
    $query = "SELECT * FROM api_techniciantoken";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultalltokens = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $alltokens = array_column($resultalltokens, "token");

    // check if in api_techniciantoken there is a token for this user that is still valid (not expired)
    $query = "SELECT * FROM api_techniciantoken WHERE email = ? AND date_created > DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultpresenttoken = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // if there is a valid token, use it
    $resultpresenttoken ? $token = $resultpresenttoken[0]["token"] : $token = generateUniqueToken($alltokens, $email, "technician");

    $_SESSION["email"] = $email;
    $_SESSION["name"] = $result[0]["name"];
    $_SESSION["surname"] = $result[0]["surname"];

    setcookie("token", $token, time() + 86400, "/");
    $_SESSION["sudo"] = true;
    
    header("Location: ../tecnici/index.php");
    exit();
}

header("Location: ../index.php");
?>