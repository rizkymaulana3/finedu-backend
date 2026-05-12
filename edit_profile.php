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

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Body request tidak valid (bukan JSON)"]);
    exit();
}

$id    = isset($input['id'])        ? (int)$input['id'] : 0;
$name  = trim($input['full_name']   ?? '');
$phone = trim($input['phone']       ?? '');

if ($id <= 0 || !$name) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID dan nama wajib diisi"]);
    exit();
}

// Cek user ada
$cek = $conn->prepare("SELECT id FROM users WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User tidak ditemukan"]);
    $cek->close();
    exit();
}
$cek->close();

$stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $phone, $id);

if ($stmt->execute()) {
    echo json_encode([
        "status"  => "success",
        "message" => "Profil berhasil diperbarui",
        "user"    => ["id" => $id, "name" => $name, "phone" => $phone]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal memperbarui profil: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
