<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

if (empty($_GET['id'])) {
    die('Achievement ID is required');
}

$stmt = $conn->prepare("SELECT * FROM achievements WHERE id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$achievement = $stmt->get_result()->fetch_assoc();

if (!$achievement) {
    die('Achievement not found');
}
?>

<div class="modal-header">
    <h5 class="modal-title">Edit Achievement</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form id="editAchievementForm" action="javascript:void(0);" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $achievement['id']; ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($achievement['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($achievement['description']); ?></textarea>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Achievement Date</label>
                <input type="date" class="form-control" name="achievement_date" value="<?php echo $achievement['achievement_date']; ?>" required>
            </div>
            <!--<div class="col-md-6">-->
            <!--    <label class="form-label">Category</label>-->
            <!--    <select class="form-control" name="category" required>-->
            <!--        <option value="">Select Category</option>-->
            <!--        <?php-->
            <!--        $categories = ['Award', 'Recognition', 'Milestone', 'Other'];-->
            <!--        foreach ($categories as $category) {-->
            <!--            $selected = ($achievement['category'] == $category) ? 'selected' : '';-->
            <!--            echo "<option value=\"$category\" $selected>$category</option>";-->
            <!--        }-->
            <!--        ?>-->
            <!--    </select>-->
            <!--</div>-->
        </div>
        <div class="mb-3">
            <label class="form-label">Current Image</label>
            <div class="mb-2">
                <img src="https://sarathicooperative.org/admin/uploads/achievements/<?php echo $achievement['image_path']; ?>" class="rounded" style="max-height: 100px;">
            </div>
            <label class="form-label">New Image (optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-control" name="status" required>
                <option value="1" <?php echo $achievement['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo $achievement['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Achievement</button>
    </div>
</form>

<script>
$('#editAchievementForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    
    $.ajax({
        url: 'update-achievement.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#editAchievementModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the achievement');
        }
    });
});
</script>