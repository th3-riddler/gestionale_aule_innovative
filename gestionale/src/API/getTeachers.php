<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$query = "SELECT name, surname, email, subject_name FROM teacher INNER JOIN teacher_subject USING(email) INNER JOIN subject USING(subject_id);";

$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$teachers = array();

foreach ($result as $row) {
    $found = false;
    for ($i = 0; $i < count($teachers); $i++) {
        if ($teachers[$i]["email"] == $row["email"]) {
            $teachers[$i]["subjects"][] = $row["subject_name"];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $teachers[] = [
            "name" => $row["name"],
            "surname" => $row["surname"],
            "email" => $row["email"],
            "subjects" => [$row["subject_name"]]
        ];
    }
}


echo json_encode($teachers);
?>