$(document).ready(function() {
    $('.card-click').click(function(event) {
        $(event.target).closest('.card-click').find('.flip').toggleClass('d-none invisible d-block visible');
    });
});
