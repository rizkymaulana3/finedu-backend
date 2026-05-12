<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db.php';

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Parameter user_id tidak valid"]);
    exit();
}

$stmt = $conn->prepare("SELECT modul_id FROM modul_selesai WHERE user_id = ? ORDER BY modul_id ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$modul_selesai = [];
while ($row = $result->fetch_assoc()) {
    $modul_selesai[] = (int)$row['modul_id'];
}

$stmt->close();
$conn->close();

echo json_encode([
    "status"        => "success",
    "modul_selesai" => $modul_selesai,
    "total"         => count($modul_selesai),
]);
?>
