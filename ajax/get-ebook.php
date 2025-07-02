<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

if (empty($_GET['id'])) {
    die('E-book ID is required');
}

$stmt = $conn->prepare("SELECT * FROM ebooks WHERE id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();

if (!$ebook) {
    die('E-book not found');
}
?>

<div class="modal-header">
    <h5 class="modal-title">Edit E-Book</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form id="editEbookForm" action="javascript:void(0);" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $ebook['id']; ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($ebook['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Author</label>
            <input type="text" class="form-control" name="author" value="<?php echo htmlspecialchars($ebook['author']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="book_date">
        </div>
        <div class="mb-3">
            <label class="form-label">Current Cover Image</label>
            <div class="mb-2">
                <img src="https://sarathicooperative.org/admin/uploads/ebooks/covers<?php echo $ebook['cover_image']; ?>" class="rounded" style="max-height: 100px;">
            </div>
            <label class="form-label">New Cover Image (optional)</label>
            <input type="file" class="form-control" name="cover_image" accept="image/*">
        </div>
        <div class="mb-3">
            <label class="form-label">Current PDF File: <?php echo basename($ebook['pdf_file']); ?></label>
            <div class="mb-2">
                <a href="https://sarathicooperative.org/admin/uploads/ebooks/files<?php echo $ebook['pdf_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> View Current PDF
                </a>
            </div>
            <label class="form-label">New PDF File (optional)</label>
            <input type="file" class="form-control" name="pdf_file" accept=".pdf">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-control" name="status" required>
                <option value="1" <?php echo $ebook['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo $ebook['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update E-Book</button>
    </div>
</form>

<script>
$('#editEbookForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    
    $.ajax({
        url: '/sarathi/admin/ajax/update-ebook.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#editEbookModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the e-book');
        }
    });
});
</script>