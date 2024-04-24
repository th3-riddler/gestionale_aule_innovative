<?php
// require AFTER db.php
function validateToken($token, $sudo = false) {
    global $conn;
    $query = "SELECT * FROM api_teachertoken WHERE token = ? AND date_created > DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resulttokentecher = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (empty($resulttokentecher)) {
        $query = "SELECT * FROM api_techniciantoken WHERE token = ? AND date_created > DATE_SUB(NOW(), INTERVAL 1 DAY)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $resulttokentechnician = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if (empty($resulttokentechnician)) {
            echo json_encode(array("message" => "Token not valid"));
            exit();
        }
    } else {
        if ($sudo) {
            echo json_encode(array("message" => "Unauthorized access"));
        }
    }
}
?>