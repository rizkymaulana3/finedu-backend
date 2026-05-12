<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
    exit();
}

include 'db.php';

$input    = json_decode(file_get_contents("php://input"), true);
$user_id  = isset($input["user_id"])  ? (int)$input["user_id"]  : 0;
$modul_id = isset($input["modul_id"]) ? (int)$input["modul_id"] : 0;
$slide_id = isset($input["slide_id"]) ? (int)$input["slide_id"] : 0;

if ($user_id <= 0 || $modul_id <= 0 || $slide_id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "user_id, modul_id, dan slide_id wajib diisi"]);
    exit();
}

// Insert slide selesai (abaikan jika sudah ada)
$stmt = $conn->prepare("INSERT IGNORE INTO slide_selesai (user_id, modul_id, slide_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $user_id, $modul_id, $slide_id);
$stmt->execute();
$stmt->close();

// Hitung slide selesai untuk modul ini
$count = $conn->prepare("SELECT COUNT(*) AS total FROM slide_selesai WHERE user_id = ? AND modul_id = ?");
$count->bind_param("ii", $user_id, $modul_id);
$count->execute();
$total = (int)$count->get_result()->fetch_assoc()["total"];
$count->close();
$conn->close();

echo json_encode([
    "status"              => "success",
    "total_slide_selesai" => $total,
]);
?>
