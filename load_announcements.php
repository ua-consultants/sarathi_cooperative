<?php
// include 'config.php'; // Uncomment and adjust as needed
$conn = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

// Check if date is provided
if (!isset($_POST['date']) || empty($_POST['date'])) {
    echo '<div class="alert alert-warning">No date specified.</div>';
    exit;
}

$selected_date = $_POST['date'];

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    echo '<div class="alert alert-danger">Invalid date format.</div>';
    exit;
}

try {
    // Query to get announcements for the selected date
    $sql = "SELECT title, content, media_type, media_url, created_at 
            FROM announcements 
            WHERE DATE(created_at) = ? AND status = 'active' 
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="announcement-item">';
            
            // Add media if available
            if ($row['media_type'] == 'image' && !empty($row['media_url'])) {
                echo '<div class="announcement-media">';
                echo '<img src="' . htmlspecialchars($row['media_url']) . '" class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: cover;" alt="Announcement Image">';
                echo '</div>';
            }
            
            echo '<h5 class="announcement-title">' . htmlspecialchars($row['title']) . '</h5>';
            echo '<div class="announcement-content">' . nl2br(htmlspecialchars($row['content'])) . '</div>';
            echo '<div class="announcement-date">';
            echo '<i class="fas fa-clock me-1"></i>';
            echo date('g:i A', strtotime($row['created_at']));
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="text-center py-4">';
        echo '<i class="fas fa-info-circle text-muted fs-3 mb-3"></i>';
        echo '<p class="text-muted mb-0">No announcements found for this date.</p>';
        echo '</div>';
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading announcements: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>