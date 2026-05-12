<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

echo json_encode([
    "status"  => "success",
    "message" => "FinEdu Backend is Running",
    "version" => "1.0.0",
    "info"    => "Please use specific endpoints for API calls.",
    "endpoints" => [
        "POST /login.php",
        "POST /register.php",
        "GET  /get_profile.php?id=",
        "POST /edit_profile.php",
        "GET  /get_modul.php?user_id=",
        "GET  /get_progress.php?user_id=",
        "POST /selesai_modul.php",
        "POST /simpan_slide.php",
    ]
]);
?>
