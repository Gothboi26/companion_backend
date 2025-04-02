<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$dsn = "mysql:host=tramway.proxy.rlwy.net;port=23857;dbname=railway";
$username = "root";
$password = "UjKxiGoBsHYBQMLRNjwPTMvFVFrTVLqk";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

// GET: Fetch all appointments with username
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT appointments.*, users.username 
                         FROM appointments 
                         JOIN users ON users.id = appointments.user_id 
                         ORDER BY date DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}

// POST: Update status and optionally remarks
if ($method === 'POST' && isset($input['appointment_id'], $input['status'])) {
    $stmt = $pdo->prepare("UPDATE appointments 
                           SET status = :status, remarks = :remarks 
                           WHERE id = :id");
    $stmt->execute([
        ':status' => $input['status'],
        ':remarks' => $input['remarks'] ?? null,
        ':id' => $input['appointment_id']
    ]);
    echo json_encode(['success' => true, 'message' => 'Status and remarks updated']);
    exit();
}

// PUT: Update service, date, time, and optionally remarks
if (
    $method === 'PUT' &&
    isset($input['appointment_id'], $input['service'], $input['date'], $input['time'])
) {
    $stmt = $pdo->prepare("UPDATE appointments 
                           SET service = :service, date = :date, time = :time, remarks = :remarks 
                           WHERE id = :id");
    $stmt->execute([
        ':service' => $input['service'],
        ':date' => $input['date'],
        ':time' => $input['time'],
        ':remarks' => $input['remarks'] ?? null,
        ':id' => $input['appointment_id']
    ]);
    echo json_encode(['success' => true, 'message' => 'Appointment updated']);
    exit();
}

// DELETE: Remove an appointment
if ($method === 'DELETE' && isset($input['appointment_id'])) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = :id");
    $stmt->execute([':id' => $input['appointment_id']]);
    echo json_encode(['success' => true, 'message' => 'Appointment deleted']);
    exit();
}

echo json_encode(['error' => 'Invalid request']);
?>
