<?php
header("Content-Type: application/json");
require_once("db.php");
require_once('validateToken.php');

$token = $_GET["token"] ?? $_COOKIE["token"] ?? "";
validateToken($token, true);

$result = $_POST;
$query = "SELECT pc_max FROM cart WHERE id = ?";
$stmt = $conn->prepare($query);
$id = intval($result["current_cart"]) ?? 1;
$stmt->bind_param("i", $id);
$stmt->execute();
$pc_numbers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0];

$query = "UPDATE cart SET";
$payload = array();
$paramsType = "";

foreach ($result as $key => $value) {
    if ($key == "current_cart") { continue; }
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

array_push($payload, intval($result["current_cart"]) ?? 1);

$paramsType .= "i";

$stmt = $conn->prepare($query);
$stmt->bind_param($paramsType, ...$payload);
$stmt->execute();
$stmt->close();

header("Location: ../tecnici/setCart.php");
?>