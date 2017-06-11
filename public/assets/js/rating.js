// Rating Widget
var selectedRating = selectedRating || 0;
console.log('selected rating: ' + selectedRating)
$('.rating-active > input[type="radio"]').on('change',function() {
  selectedRating = $(this).filter(':checked').val();
  console.log('selected rating changed: ' + selectedRating)
});
$('.rating-active > label').on({
  'mouseover': function() {
    $(this).prevAll().addBack().addClass('rating-selected');
  },
  'mouseout': function() {
    $(this).siblings('label').addBack().filter(function(i) {
      return ((i+1) > selectedRating);
    }).removeClass('rating-selected');
  }
});

// Validate that a rating star has been selected before form submit
$('.submitReviewForm').on('submit', function(e) {
  if ($(this).find('input[type="radio"][name="rating"]:checked').val()) {
    return true;
  } else {
    e.preventDefault();
    $(this).find('input[type="radio"][name="rating"]').parents('.form-group').addClass('has-danger');
    alert('Please select a star rating.');
    return false;
  }
});