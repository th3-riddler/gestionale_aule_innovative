<?php
session_start();
session_destroy();
header("Content-Type: application/json");

header("Location: ../index.php");
?>