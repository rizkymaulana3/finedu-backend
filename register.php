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

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Body request tidak valid (bukan JSON)"]);
    exit();
}

$name     = trim($data["name"]     ?? "");
$email    = trim($data["email"]    ?? "");
$phone    = trim($data["phone"]    ?? "");
$password = trim($data["password"] ?? "");

if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Field name, email, dan password wajib diisi"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Format email tidak valid"]);
    exit();
}

// Cek duplikat email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["status" => "error", "message" => "Email sudah digunakan"]);
    $stmt->close();
    exit();
}
$stmt->close();

// Insert user baru
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $hash);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        "status"  => "success",
        "message" => "Registrasi berhasil",
        "user_id" => (int)$conn->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan data"]);
}

$stmt->close();
$conn->close();
?>
