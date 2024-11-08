$(document).ready(function() {
    function flip(selector) {
        $(selector).closest('.card-click').find('.flip').toggleClass('d-none invisible d-inline visible');
    }

    $('.card-text').click(function(event) {
        flip(event.target);
    });

    $('.card-header').click(function(event) {
        flip(event.target);
    });

});
