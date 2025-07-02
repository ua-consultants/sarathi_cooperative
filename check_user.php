<?php
header('Content-Type: application/json');

require_once 'admin/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');

    if (!$email && !$phone) {
        echo json_encode(['exists' => false, 'message' => 'No data provided']);
        exit;
    }

    $query = "SELECT id FROM members WHERE";
    $conditions = [];
    $params = [];

    if ($email) {
        $conditions[] = "email = ?";
        $params[] = $email;
    }
    if ($phone) {
        $conditions[] = "phone = ?";
        $params[] = $phone;
    }

    $query .= " " . implode(" OR ", $conditions);

    $stmt = $conn->prepare($query);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->store_result();

    // if ($stmt->num_rows > 0) {
    //     echo json_encode([
    //         'exists' => true,
    //         'message' => 'User already registered with this email or phone.'
    //     ]);
    // } else {
    //     echo json_encode(['exists' => false]);
    // }
}
?>