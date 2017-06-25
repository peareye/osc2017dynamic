// Delete prompt handler
$('body').on('click', '.deleteButton', function() {
  var reply = confirm('Are you sure you want to delete?');
  return reply;
});

// Show alert on request
var showAlert = function(message, status = 'alert-info') {
    $('.alert').removeClass('alert-info alert-success alert-danger alert-warning').addClass(status).find('.alert-message').html(message).end().show();
}

// Dismiss alert without deleting element
$('.alert').on('click', '.alert-close', function() {
    $(this).closest('.alert').hide().find('.alert-message').html('');
});
