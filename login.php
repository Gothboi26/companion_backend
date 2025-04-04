<?php
session_start();
header("Access-Control-Allow-Origin: https://seniorcare-flt3.onrender.com");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(json_encode(["status" => "success", "message" => "CORS OK"]));
}

// Connect to Database
$conn = new mysqli("tramway.proxy.rlwy.net", "root", "UjKxiGoBsHYBQMLRNjwPTMvFVFrTVLqk", "railway", 23857);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Read JSON input
$rawData = file_get_contents("php://input");
if (!$rawData) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Empty request body"]);
    exit();
}

$data = json_decode($rawData, true);
if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing username or password"]);
    exit();
}

// Fetch user info
$username = $data['username'];
$password = $data['password'];
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
    echo json_encode(["status" => "success", "role" => $role, "message" => "Login successful"]);
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}

// Ensure content is sent
$stmt->close();
$conn->close();
?>
