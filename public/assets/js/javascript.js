// jQuery plugin to prevent double submission of forms
jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    var $form = $(this);

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

$('form').preventDoubleSubmission();

// Review Pagination. Interrupt page request and fetch reviews behind the scenes
$('#review-set').on('click', '.pagination a', function(e) {
	e.preventDefault();
	var resetTopOffset = $("#review-set").offset().top - 170;

	// Fetch rendered set of reviews
	$.ajax({
	  url: this.href,
	  success: function(response) {
	  	$('#review-set').fadeOut(250, function() {
	  		$('html, body').animate({
	  			scrollTop: resetTopOffset
	  		}, 250);
	  		$(this).html(response).fadeIn(500);
	  	})
	  }
	});
});
