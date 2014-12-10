jQuery('document').ready(function($) {
    $('.extendable-excerpt-action').click(function(event) {
        event.preventDefault();
        var excerpt = $(this).closest('.perfect-excerpt');
        excerpt.find('.extendable-excerpt-action').remove();
        excerpt.find('.extended-excerpt').fadeIn(1000);
    });
});