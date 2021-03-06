// jQuery plugin to prevent double submission of forms
jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    let $form = $(this);

    if ($form.data('submitted') === true) {
      // Previously submitted - don't submit again
      e.preventDefault();
    } else {
      // Mark it so that the next submit can be ignored
      $form.data('submitted', true);
    }
  });

  // Keep chainability
  return this;
};

// Delete prompt handler
$('body').on('click', '.deleteButton', function(e) {
  if (!confirm('Are you sure you want to delete?')) {
    e.preventDefault();
    return;
  }
});

// Show alert on request
let showAlert = function(message, status = 'alert-info') {
    $('.alert').removeClass('alert-info alert-success alert-danger alert-warning').addClass(status).find('.alert-message').html(message).end().show();
}

// Dismiss alert without deleting element
$('.alert').on('click', '.alert-close', function() {
    $(this).closest('.alert').hide().find('.alert-message').html('');
});

$('form').preventDoubleSubmission();

$(document).ready(function() {
	if (window.location.pathname.match('showreviews')) {
		let pending = parseInt($('#myTab span.badge').html());
		if (pending > 0) {
			$('#myTab a[href="#pending"]').tab('show');
		}
	}
});
