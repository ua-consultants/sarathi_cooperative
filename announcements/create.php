<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$page = 'announcements';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = $_POST['content'];
        $media_type = filter_input(INPUT_POST, 'media_type', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $end_date = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
        
        // Validate end_date format if provided
        if (!empty($end_date)) {
            $date = DateTime::createFromFormat('Y-m-d', $end_date);
            if (!$date || $date->format('Y-m-d') !== $end_date) {
                throw new Exception('Invalid end date format');
            }
        } else {
            $end_date = null; // Set to null if empty
        }
        
        // Handle file upload
        $media_url = '';
        if (!empty($_FILES['media']['name'])) {
            $upload_dir = '../uploads/announcements/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
            
            if (!in_array($file_ext, $allowed_extensions)) {
                throw new Exception('Invalid file type');
            }
            
            $file_name = uniqid() . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;
            
            if (!move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
                throw new Exception('Failed to upload file');
            }
            $media_url = 'https://sarathicooperative.org/admin/uploads/announcements/' . $file_name;
        }

        $stmt = $conn->prepare("INSERT INTO announcements (title, content, media_type, media_url, status, created_at, end_date) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $title, $content, $media_type, $media_url, $status, $end_date);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create announcement: ' . $stmt->error);
        }

        $_SESSION['success'] = 'Announcement created successfully!';
        header('Location: index.php');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Create Announcement</h1>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Media Type</label>
                        <select name="media_type" class="form-select" id="mediaType">
                            <option value="none">None (Text Only)</option>
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    <div class="mb-3" id="mediaUpload" style="display: none;">
                        <label class="form-label">Upload Media</label>
                        <input type="file" name="media" class="form-control" accept=".jpg,.jpeg,.png,.gif,.mp4,.webm">
                        <small class="text-muted">
                            Supported formats: Images (JPG, PNG, GIF), Videos (MP4, WebM)
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" id="editor"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">End Date (Optional)</label>
                        <input type="date" name="end_date" class="form-control">
                        <small class="text-muted">Leave blank for announcements without expiry date</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Announcement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
            placeholder: 'Enter announcement content here...'
        })
        .then(editor => {
            // Store editor instance
            window.editor = editor;
            
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const editorData = editor.getData();
                if (!editorData.trim()) {
                    e.preventDefault();
                    alert('Please enter content');
                    return false;
                }
            });
        })
        .catch(error => {
            console.error(error);
        });

    // Media type toggle
    $('#mediaType').on('change', function() {
        if (this.value === 'none') {
            $('#mediaUpload').hide();
        } else {
            $('#mediaUpload').show();
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>