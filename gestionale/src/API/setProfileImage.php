<?php
session_start();
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$email = $_GET["email"] ?? "";
$work = checkUserAuth($email, $token);

$_SESSION["error"] = 0;

function backToProfile($work){
    if($work == "technician"){
        header("Location: ../tecnici/technicianProfile.php");
    }
    else if($work == "teacher"){
        header("Location: ../docenti/teacherProfile.php");
    }
    exit();
}

if(isset($_FILES['profileImageSet'])) {
    if ($_FILES['profileImageSet']['error'] !== UPLOAD_ERR_OK) { //controllo se il file è stato caricato correttamente
        $_SESSION["error"] = 3;
        backToProfile($work);
        //die('File upload failed with error code ' . $_FILES['profileImageSet']['error']);
    }
    $path = $_FILES['profileImageSet']['tmp_name'];
    if (!file_exists($path)) {
        $_SESSION["error"] = 3;
        backToProfile($work);
    }
    $size = $_FILES['profileImageSet']['size'];
    try{
        $type = getimagesize($path)[2];
    }
    catch(Exception $e){
        $_SESSION["error"] = 2;
        backToProfile($work);
    }
    //die(var_dump($type));


    // php limita il file a 2MB di default, limito a 64KB perché immagini BLOB
    if($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG && $type != IMAGETYPE_GIF){
        $_SESSION["error"] = 2;
    }
    else if($size > 64000){ //64KB
        $_SESSION["error"] = 1;
    }
}
else{
    $_SESSION["error"] = 3;
}


if($_SESSION["error"] != 0){
    backToProfile($work);
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