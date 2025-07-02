<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$page = 'announcements';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$announcement = $stmt->get_result()->fetch_assoc();

if (!$announcement) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = $_POST['content'];
    $media_type = filter_input(INPUT_POST, 'media_type', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    
    // Handle new file upload
    $media_url = $announcement['media_url'];
    if (!empty($_FILES['media']['name'])) {
        $upload_dir = '../../uploads/announcements/';
        
        // Delete old file if exists
        if ($media_url && file_exists('../../' . ltrim($media_url, '/sarathi/'))) {
            unlink('../../' . ltrim($media_url, '/sarathi/'));
        }
        
        $file_ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
            $media_url = '/sarathi/uploads/announcements/' . $file_name;
        }
    }

    $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ?, media_type = ?, media_url = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $content, $media_type, $media_url, $status, $id);
    
    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Edit Announcement</h1>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Media Type</label>
                        <select name="media_type" class="form-select" id="mediaType">
                            <option value="none" <?php echo $announcement['media_type'] === 'none' ? 'selected' : ''; ?>>None (Text Only)</option>
                            <option value="image" <?php echo $announcement['media_type'] === 'image' ? 'selected' : ''; ?>>Image</option>
                            <option value="video" <?php echo $announcement['media_type'] === 'video' ? 'selected' : ''; ?>>Video</option>
                        </select>
                    </div>

                    <div class="mb-3" id="mediaUpload" style="display: <?php echo $announcement['media_type'] !== 'none' ? 'block' : 'none'; ?>;">
                        <?php if ($announcement['media_url']): ?>
                        <div class="mb-2">
                            <label class="form-label">Current Media:</label>
                            <?php if ($announcement['media_type'] === 'image'): ?>
                                <img src="<?php echo $announcement['media_url']; ?>" alt="Current media" style="max-width: 200px; display: block;">
                            <?php else: ?>
                                <video src="<?php echo $announcement['media_url']; ?>" controls style="max-width: 200px; display: block;"></video>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <label class="form-label">Upload New Media (leave empty to keep current)</label>
                        <input type="file" name="media" class="form-control" accept=".jpg,.jpeg,.png,.gif,.mp4,.webm">
                        <small class="text-muted">
                            Supported formats: Images (JPG, PNG, GIF), Videos (MP4, WebM)
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" id="editor" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $announcement['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $announcement['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Announcement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#editor'))
    .catch(error => {
        console.error(error);
    });

$('#mediaType').on('change', function() {
    if (this.value === 'none') {
        $('#mediaUpload').hide();
    } else {
        $('#mediaUpload').show();
    }
});
</script>

<?php include '../includes/footer.php'; ?>