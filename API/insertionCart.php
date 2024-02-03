<?php
session_start();
require_once("db.php");

$result = $_POST;
$query = "SELECT pc_max,pc_disp FROM carrello WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["current_cart"]);
$stmt->execute();
$pc_numbers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0];

if ($result["pc_max"] != "") {
    if ($result["pc_disp"] == "") {
        if ($pc_numbers["pc_disp"] > $result["pc_max"]) {
            $result["pc_disp"] = $result["pc_max"];
        }
    } else {
        if ($result["pc_disp"] > $result["pc_max"]) {
            $result["pc_disp"] = $result["pc_max"];
        }
    }
}

if ($result["pc_disp"] != "") {
    if ($result["pc_max"] == "") {
        if ($pc_numbers["pc_max"] < $result["pc_disp"]) {
            $result["pc_disp"] = $pc_numbers["pc_max"];
        }
    }
}


$query = "UPDATE carrello SET";
$payload = array();
$paramsType = "";

foreach ($result as $key => $value) {
    if ($value != null) {
        $query .= " $key = ?,";
        array_push($payload, $value);
        if (is_numeric($value)) {
            $paramsType .= "i";
        } else {
            $paramsType .= "s";
        }
    }
}

$query = substr($query, 0, -1);
$query .= " WHERE id = ?";

array_push($payload, $_SESSION["current_cart"]);

$paramsType .= "i";



$stmt = $conn->prepare($query);
$stmt->bind_param($paramsType, ...$payload);
$stmt->execute();
$stmt->close();



header("Location: ../tecnici/insertionCart.php");
