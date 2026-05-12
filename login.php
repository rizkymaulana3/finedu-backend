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

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Body request tidak valid (bukan JSON)"]);
    exit();
}

$email    = trim($data['email']    ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Email dan password wajib diisi"]);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, email, phone, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Email tidak ditemukan"]);
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Password salah"]);
    exit();
}

// Generate token sederhana
$token = bin2hex(random_bytes(32));

echo json_encode([
    "status"  => "success",
    "message" => "Login berhasil",
    "token"   => $token,
    "user"    => [
        "id"    => (int)$user['id'],
        "name"  => $user['name'],
        "email" => $user['email'],
        "phone" => $user['phone'],
    ]
]);

$stmt->close();
$conn->close();
?>
