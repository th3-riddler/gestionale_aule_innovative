<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token);

$data = json_decode(file_get_contents("php://input"), true);

$name = $data["name"] ?? "";
$surname = $data["surname"] ?? "";
$email = $data["email"] ?? "";
$subjects = $data["subjects"] ?? array();

if ($name == "" || $surname == "" || $email == "" || empty($subjects)) {
    echo json_encode(["status" => "error", "message" => "Missing fields", "data" => $data]);
    exit();
} else {
    $query = "INSERT INTO teacher (name, surname, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $name, $surname, $email);
    $stmt->execute();


    $subject_ids = array();
    foreach ($subjects as $subject) {
        $query = "SELECT subject_id FROM subject WHERE subject_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $subject_ids[] = $row["subject_id"];
    }


    $param = "";
    $elem = array();

    $query = "INSERT INTO teacher_subject VALUES ";
    foreach ($subject_ids as $id) {
        $query .= "(?, ?), ";
        $elem[] = $email;
        $elem[] = $id;
    }

    $query = substr($query, 0, -2);
    $query .= ";";

    $stmt = $conn->prepare($query);
    
    foreach ($subject_ids as $id) {
        $param .= "si";
    }
    $stmt->bind_param($param, ...$elem);
    $stmt->execute();

    echo json_encode(["status" => "successfully added teacher"]);
}
?>