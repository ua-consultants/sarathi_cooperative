<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();
$page = 'announcements';
include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Announcements</h1>
            <a href="create.php" class="btn btn-primary">Create New Announcement</a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Media Type</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
                            while($announcement = $announcements->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                                <td><?php echo ucfirst($announcement['media_type']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $announcement['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($announcement['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-sm btn-danger delete-announcement" data-id="<?php echo $announcement['id']; ?>">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.delete-announcement').on('click', function() {
        if(confirm('Are you sure you want to delete this announcement?')) {
            const id = $(this).data('id');
            const button = $(this);
            
            // Disable button to prevent double-clicks
            button.prop('disabled', true).text('Deleting...');
            
            $.ajax({
                url: 'delete.php',
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        // Remove the row from table or reload page
                        button.closest('tr').fadeOut(function() {
                            $(this).remove();
                        });
                        // Or use location.reload(); if you prefer
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                        button.prop('disabled', false).text('Delete');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                    let errorMessage = 'Failed to delete announcement';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch(e) {
                        // If response is not JSON, use default message
                    }
                    
                    alert('Error: ' + errorMessage);
                    button.prop('disabled', false).text('Delete');
                }
            });
        }
    });
});
</script>
<?php include '../includes/footer.php'; ?>