<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$email = $_GET["email"] ?? "";
$work = checkUserAuth($email, $token);

$error = 0;

if(isset($_FILES['profileImageSet'])) {
    if ($_FILES['profileImageSet']['error'] !== UPLOAD_ERR_OK) { //controllo se il file è stato caricato correttamente
        if($work == "technician"){
            header("Location: ../tecnici/technicianProfile.php?error=4");
        }
        else if($work == "teacher"){
            header("Location: ../docenti/teacherProfile.php?error=4");
        }
        exit();
        //die('File upload failed with error code ' . $_FILES['profileImageSet']['error']);
    }
    $path = $_FILES['profileImageSet']['tmp_name'];
    if (!file_exists($path)) {
        die('File does not exist at the specified path');
    }
    $size = $_FILES['profileImageSet']['size'];
    $type = getimagesize($path)[2];
    //die(var_dump($type));


    // php limita il file a 2MB di default, ma lo limitiamo a 100KB per evitare sovraccarichi nel server, nonostante le immagini profilo dovrebbero essere piccole (<50KB)
    if($size > 100000){ //100KB
        $error = 1;
    }
    else if($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG && $type != IMAGETYPE_GIF){
        $error = 2;
    }
}
else{
    $error = 3;
}


if($error != 0){
    if($work == "technician"){
        header("Location: ../tecnici/technicianProfile.php?error=" . $error);
        exit();
    }
    else if($work == "teacher"){
        header("Location: ../docenti/teacherProfile.php?error=" . $error);
        exit();
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