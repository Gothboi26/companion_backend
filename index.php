<?php
// Allow CORS
header("Access-Control-Allow-Origin: https://seniorcare-flt3.onrender.com");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if request is from a browser
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    header("Location: login.php");
    exit();
}

// If API request (no user agent), return JSON
echo json_encode(["status" => "error", "message" => "Invalid request"]);
?>
