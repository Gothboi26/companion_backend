<?php
session_start();
ob_start(); // Start output buffering

// ✅ Enable PHP Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ CORS setup (Temporarily allowing all origins for debugging)
header("Access-Control-Allow-Origin: *"); // Change to specific origin later
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// ✅ Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Database Connection
$conn = new mysqli("tramway.proxy.rlwy.net", "root", "UjKxiGoBsHYBQMLRNjwPTMvFVFrTVLqk", "railway", 23857);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// ✅ Add default admin if not exists
$default_username = "admin";
$default_password = password_hash("admin123", PASSWORD_DEFAULT);
$default_role = "admin";

$sql = "INSERT IGNORE INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $default_username, $default_password, $default_role);
$stmt->execute();
$stmt->close();

// ✅ Get request data
$rawData = file_get_contents("php://input");
if (!$rawData) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Empty request body']);
    exit();
}

$data = json_decode($rawData, true);
if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing username or password']);
    exit();
}

$username = $data['username'];
$password = $data['password'];

// ✅ Fetch user info
$sql = "SELECT id, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($user_id, $hashed_password, $role);

if ($stmt->num_rows > 0 && $stmt->fetch() && password_verify($password, $hashed_password)) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;

    session_write_close();

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'role' => $role,
        'message' => 'Login successful',
    ]);
} else {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
}

// ✅ Ensure content length is sent
header("Content-Length: " . ob_get_length());

$stmt->close();
$conn->close();
ob_end_flush(); // Flush output buffer
?>
