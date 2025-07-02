<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit('Invalid request');
}

$stmt = $conn->prepare("SELECT * FROM memories WHERE id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$memory = $stmt->get_result()->fetch_assoc();

if (!$memory) {
    exit('Memory not found');
}
?>

<div class="modal-header">
    <h5 class="modal-title">Edit Memory</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form id="editMemoryForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $memory['id']; ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($memory['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($memory['description']); ?></textarea>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Event Date</label>
                <input type="date" class="form-control" name="event_date" value="<?php echo $memory['event_date']; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Venue</label>
                <input type="text" class="form-control" name="venue" value="<?php echo htmlspecialchars($memory['venue']); ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Current Image</label>
            <div class="mb-2">
                <img src="/sarathi/uploads/<?php echo $memory['image_path']; ?>" style="max-width: 200px;">
            </div>
            <input type="file" class="form-control" name="image" accept="image/*">
            <small class="text-muted">Leave empty to keep current image</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="active" <?php echo $memory['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $memory['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Memory</button>
    </div>
</form>

<script>
$('#editMemoryForm').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: '/sarathi/admin/ajax/update-memory.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }
    });
});
</script>