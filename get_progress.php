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

$stmt = $conn->prepare("
    SELECT 
        ms.id,
        u.name       AS nama_user,
        ms.modul_id,
        ms.selesai_at
    FROM modul_selesai ms
    JOIN users u ON ms.user_id = u.id
    WHERE ms.user_id = ?
    ORDER BY ms.id ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['id']       = (int)$row['id'];
    $row['modul_id'] = (int)$row['modul_id'];
    $data[]          = $row;
}

$stmt->close();
$conn->close();

echo json_encode([
    "status" => "success",
    "total"  => count($data),
    "data"   => $data
]);
?>
