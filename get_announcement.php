<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid announcement ID']);
    exit;
}

$announcement_id = intval($_GET['id']);

$sql = "SELECT id, title, content, media_type, media_url, created_at FROM announcements WHERE id = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $announcement = $result->fetch_assoc();
    echo json_encode(['success' => true, 'announcement' => $announcement]);
} else {
    echo json_encode(['success' => false, 'message' => 'Announcement not found']);
}

$conn->close();
?>