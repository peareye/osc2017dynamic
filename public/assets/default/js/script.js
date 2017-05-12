// Archive navigation
$('.archive-navigation .archive-item').on('click', function(e) {
	e.preventDefault();
	$(this).siblings('.archive-'+$(this).data('archive')).slideToggle();
});

// Submit comment
$('#post-comments').on('submit', '.comment-form', function(e) {
	var $submittedForm = $(this);
	$submittedForm.find('button').addClass('disabled').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Submitting...');
	$.ajax({
		url: basePath + '/savecomment',
		method: 'POST',
		processData: false,
		contentType:false,
		data:  new FormData(this),
		success: function(r) {
			console.log(r)
			if (r.status == 1) {
				$submittedForm.find('button').removeClass('disabled').html('Submit');
				$submittedForm.siblings('h2').remove();
				$submittedForm.siblings('a').remove();
				$submittedForm.replaceWith(r.source).fadeIn('fast');
			};
		},
		error: function(r) {
			console.log('Comment submit failed')
		}
	});

	return false;
});

// Show comment reply form
var $commentForm = $('#post-comments .comment-form');
$('#post-comments').on('click', '.comment-reply-link', function(e) {
	e.preventDefault();
	var $reply = $commentForm.clone();
	$reply.find('input[name="reply_id"]').val($(this).data('id'));
	$(this).after($reply[0].outerHTML).toggleClass('comment-reply-link cancel-reply').html('Cancel Reply');
});

// Cancel comment reply
$('#post-comments').on('click', '.cancel-reply', function(e) {
	e.preventDefault();
	$(this).siblings('.comment-form').remove();
	$(this).toggleClass('cancel-reply comment-reply-link').html('Reply');
});

// Expand/Collapse nested comments
$('#post-comments').on('click', '.toggle-comments', function (e) {
    var $this = $(this);
    if (!$this.hasClass('panel-collapsed')) {
        $this.closest('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-minus').addClass('glyphicon-plus');
    } else {
        $this.closest('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});
