<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$email = $_GET["email"] ?? "";
$work = $_GET["work"] ?? "";

$error = 0;
if(isset($_FILES['profileImageSet'])) {
    $path = $_FILES['profileImageSet']['tmp_name'];
    $size = $_FILES['profileImageSet']['size'];
    $type = $_FILES['profileImageSet']['type'];

    if($size > 16000000){ //16MB
        $error = 1;
    }
    else if($type != "image/jpeg"){
        $error = 2;
    }
}
else{
    $error = 3;
}

if($error != 0){
    if($work == "technician"){
        header("Location: ../tecnici/technicianProfile.php?error=" . $error);
    }
    else if($work == "teacher"){
        header("Location: ../docenti/teacherProfile.php?error=" . $error);
    }
}

$fileContent = file_get_contents($path);

if($work == "technician"){
    $query = "UPDATE technician SET profileImage = ? WHERE email = ?";
}
else if($work == "teacher"){
    $query = "UPDATE teacher SET profileImage = ? WHERE email = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $fileContent, $email);
$stmt->execute();

if($work == "technician"){
    header("Location: ../tecnici/technicianProfile.php");
}
else if($work == "teacher"){
    header("Location: ../docenti/teacherProfile.php");
}
?>