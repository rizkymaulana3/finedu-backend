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

if ($user_id <= 0 || $modul_id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "user_id dan modul_id wajib diisi"]);
    exit();
}

// Cek duplikat
$cek = $conn->prepare("SELECT id FROM modul_selesai WHERE user_id = ? AND modul_id = ?");
$cek->bind_param("ii", $user_id, $modul_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    $cek->close();
    $total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM modul_selesai WHERE user_id = ?");
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total = (int)$total_stmt->get_result()->fetch_assoc()["total"];
    $total_stmt->close();
    $conn->close();
    echo json_encode([
        "status"  => "success",
        "message" => "Modul sudah pernah diselesaikan",
        "total"   => $total
    ]);
    exit();
}
$cek->close();

// Insert
$insert = $conn->prepare("INSERT INTO modul_selesai (user_id, modul_id, selesai_at) VALUES (?, ?, NOW())");
$insert->bind_param("ii", $user_id, $modul_id);

if (!$insert->execute()) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan: " . $insert->error]);
    $insert->close();
    $conn->close();
    exit();
}
$insert->close();

// Hitung total
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM modul_selesai WHERE user_id = ?");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total = (int)$total_stmt->get_result()->fetch_assoc()["total"];
$total_stmt->close();
$conn->close();

echo json_encode([
    "status"  => "success",
    "message" => "Modul berhasil diselesaikan",
    "total"   => $total
]);
?>
