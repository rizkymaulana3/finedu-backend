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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Parameter id tidak valid"]);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User tidak ditemukan"]);
    exit();
}

$user = $result->fetch_assoc();
$user['id'] = (int)$user['id'];

// Hitung modul selesai dari database
$mod_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM modul_selesai WHERE user_id = ?");
$mod_stmt->bind_param("i", $id);
$mod_stmt->execute();
$modul_selesai = (int)$mod_stmt->get_result()->fetch_assoc()["total"];
$mod_stmt->close();

echo json_encode([
    "status" => "success",
    "user"   => $user,
    "stats"  => [
        ["value" => $modul_selesai . "/16", "label" => "Modul Selesai"],
        ["value" => "88.5%",                "label" => "Rata-rata Skor"],
        ["value" => "3/6",                  "label" => "Badge Diperoleh"],
    ],
    "badges" => [
        ["icon" => "📋", "color" => "#4A90D9", "label" => "Pembelajar Keuangan",  "date" => "Diperoleh: 15 Maret 2026", "diperoleh" => true],
        ["icon" => "🏆", "color" => "#FFC107", "label" => "Master Quiz",           "date" => "Diperoleh: 20 Maret 2026", "diperoleh" => true],
        ["icon" => "⚡", "color" => "#E91E63", "label" => "Pembelajar Cepat",      "date" => "Diperoleh: 10 Maret 2026", "diperoleh" => true],
        ["icon" => "©️", "color" => "#4DC57F", "label" => "Profesional Investasi", "date" => "Belum diperoleh",          "diperoleh" => false],
        ["icon" => "⭐", "color" => "#F28E03", "label" => "Skor Sempurna",         "date" => "Belum diperoleh",          "diperoleh" => false],
        ["icon" => "🎖️", "color" => "#F44336", "label" => "Berdedikasi",           "date" => "Belum diperoleh",          "diperoleh" => false],
    ]
]);

$stmt->close();
$conn->close();
?>
