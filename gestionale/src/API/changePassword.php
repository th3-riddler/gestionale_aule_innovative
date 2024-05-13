<?php
header("Content-Type: application/json");
require_once("db.php");
require_once("validateToken.php");

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

function sha256($data)
{
    return hash("sha256", $data);
}

$data = json_decode(file_get_contents("php://input"), true);
$email = $data["technicianEmail"] ?? "";
$work = checkUserAuth($email, $token);

// $work = "technician";

$oldPassword = sha256($data["oldPassword"]) ?? "";
$newPassword = sha256($data["newPassword"]) ?? "";
$conirmPassword = sha256($data["confirmPassword"]) ?? "";

if ($newPassword != $conirmPassword) {
    echo json_encode(["status" => "error", "message" => "Le password non coincidono"]);
    exit();
}

if($work == "technician"){
    $query = "SELECT password FROM technician WHERE email = ?";
}
else if($work == "teacher"){
    $query = "SELECT password FROM teacher WHERE email = ?";
}

$stmt = $conn->prepare($query);

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$currentPassword = $result[0]['password'];

if($currentPassword != $oldPassword){
    echo json_encode(["status" => "error", "message" => "La password corrente non è corretta"]);
    exit();
}
else{
    if($work == "technician"){
        $query = "UPDATE technician SET password = ? WHERE email = ?;";
    }
    else if($work == "teacher"){
        $query = "UPDATE teacher SET password = ? WHERE email = ?;";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $newPassword, $email);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Password cambiata con successo"]);
    exit();
}

?>