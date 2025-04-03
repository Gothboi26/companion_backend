<?php
$host = "tramway.proxy.rlwy.net";
$user = "root";
$pass = "UjKxiGoBsHYBQMLRNjwPTMvFVFrTVLqk";
$db = "railway";
$port = 23857;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "❌ Connection failed: " . $conn->connect_error]));
}

$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result) {
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    echo json_encode(["status" => "success", "message" => "✅ Connected!", "tables" => $tables]);
} else {
    echo json_encode(["status" => "error", "message" => "❌ Query failed: " . $conn->error]);
}

$conn->close();
?>
