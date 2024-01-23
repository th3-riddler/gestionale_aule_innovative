<?php
session_start();

$result = $_POST;
$result["room"] = $_SESSION["current_room"];

echo json_encode($result);

?>