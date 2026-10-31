$(document).ready(function() {
    function flip(selector) {
        $(selector).closest('.card-click').find('.flip').toggleClass('d-none invisible d-inline visible');
    }

    // Delegated, so cards loaded after page load also respond.
    $(document).on('click', '.card-text, .card-header', function(event) {
        flip(event.target);
    });

    // Load a location's boxes the first time its panel is expanded.
    $(document).on('show.bs.collapse', '[data-boxes-url]', function() {
        const $target = $(this);

        // Bootstrap can deliver this event more than once per show; the state
        // guard also prevents refetching once loaded.
        if ($target.data('boxesState')) {
            return;
        }
        $target.data('boxesState', 'loading');

        $.ajax({
            url: $target.data('boxesUrl'),
            dataType: 'html'
        }).done(function(html) {
            $target.html(html);
            $target.data('boxesState', 'loaded');
        }).fail(function(xhr) {
            if (xhr.status === 401) {
                // Session expired; reloading sends the browser to the login page.
                window.location.reload();
                return;
            }
            $target.removeData('boxesState');
            $target.html(
                '<div class="alert alert-danger text-center">Unable to load boxes. Close and reopen to retry.</div>'
            );
        });
    });
});
