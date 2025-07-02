$('.approve-application').click(function() {
    const id = $(this).data('id');
    if (!confirm('Are you sure you want to approve this application?')) {
        return;
    }

    $.ajax({
        url: '/sarathi/admin/ajax/approve-application.php',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Application approved successfully');
                location.reload();
            } else {
                alert(response.message || 'Failed to approve application');
            }
        },
        error: function(xhr, status, error) {
            alert('Error: ' + (xhr.responseJSON?.message || 'Failed to process request'));
        }
    });
});