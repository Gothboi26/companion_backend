<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = "tramway.proxy.rlwy.net";
$username = "root";
$password = "UjKxiGoBsHYBQMLRNjwPTMvFVFrTVLqk";
$database = "railway";
$port = 23857;

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // ✅ Include group_chapter and use email_address
    $stmt = $conn->prepare("SELECT id, username, password, age, sex, address, health_issue, email_address, barangay_id, group_chapter FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        echo json_encode(["status" => "success", "user" => $result->fetch_assoc()]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found."]);
    }

    $stmt->close();
} else {
    // ✅ Include group_chapter and use email_address in the general list
    $sql = "SELECT id, username, age, sex, address, health_issue, email_address, barangay_id, group_chapter FROM users";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo json_encode(["status" => "success", "data" => $result->fetch_all(MYSQLI_ASSOC)]);
    } else {
        echo json_encode(["status" => "error", "message" => "No users found."]);
    }
}

$conn->close();
?>
