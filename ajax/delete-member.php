<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

try {
    $conn->begin_transaction();

    // Check if member exists and is active
    $stmt = $conn->prepare("SELECT status FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();

    if (!$member) {
        throw new Exception('Member not found');
    }

    // Soft delete by updating status
    $stmt = $conn->prepare("
        UPDATE members 
        SET status = 'inactive',
            updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete member');
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Member deleted successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}