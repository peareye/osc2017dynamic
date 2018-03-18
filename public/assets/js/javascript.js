// Review Pagination. Interrupt page request and fetch reviews behind the scenes
$('#review-set').on('click', '.pagination a', function(e) {
	e.preventDefault();
	var resetTopOffset = $("#review-set").offset().top - 170;

	// Fetch rendered set of reviews
	$.ajax({
	  url: this.href,
	  success: function(response) {
	  	$('#review-set').fadeOut('fast', function() {
	  		$('html, body').animate({
	  			scrollTop: resetTopOffset
	  		}, 500);
	  		$(this).html(response).fadeIn('fast');
	  	})
	  }
	});
});
