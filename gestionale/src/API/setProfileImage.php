<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$email = $_GET["email"] ?? "";
$work = checkUserAuth($email, $token);

$error = 0;

function backToProfile($work, $error = 0){
    if($work == "technician"){
        header("Location: ../tecnici/technicianProfile.php?error=$error");
    }
    else if($work == "teacher"){
        header("Location: ../docenti/teacherProfile.php?error=$error");
    }
    exit();
}

if(isset($_FILES['profileImageSet'])) {
    if ($_FILES['profileImageSet']['error'] !== UPLOAD_ERR_OK) { //controllo se il file è stato caricato correttamente
        $error = 3;
        backToProfile($work, $error);
    }
    $path = $_FILES['profileImageSet']['tmp_name'];
    if (!file_exists($path)) {
        $error = 3;
        backToProfile($work, $error);
    }
    $size = $_FILES['profileImageSet']['size'];
    try{
        $type = getimagesize($path)[2];
    }
    catch(Exception $e){
        $error = 2;
        backToProfile($work, $error);
    }
    //die(var_dump($type));


    // php limita il file a 2MB di default, limito a 64KB perché immagini BLOB
    if($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG && $type != IMAGETYPE_GIF){
        $error = 2;
    }
    else if($size > 64000){ //64KB
        $error = 1;
    }
}
else{
    $error = 3;
}


if($error != 0){
    backToProfile($work, $error);
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

backToProfile($work);
?>