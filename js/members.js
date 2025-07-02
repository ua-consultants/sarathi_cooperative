// Add this to your existing members.js file

$(document).ready(function() {
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    if (response.errors && response.errors.length > 0) {
                        console.log('Errors:', response.errors);
                    }
                    $('#excelFile').val('');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Failed to process the request');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});

// View member details
$('.view-member').click(function() {
    const memberId = $(this).data('id');
    $.get('/sarathi/admin/ajax/view-member.php', { id: memberId }, function(response) {
        $('#memberModal .modal-content').html(response);
        $('#memberModal').modal('show');
    });
});

// Delete member
$('.delete-member').click(function() {
    if (!confirm('Are you sure you want to delete this member?')) {
        return;
    }
    const memberId = $(this).data('id');
    $.post('/sarathi/admin/ajax/delete-member.php', { id: memberId }, function(response) {
        if (response.success) {
            location.reload();
        } else {
            alert(response.message || 'Failed to delete member');
        }
    });
});