$(document).ready(function () {
    $("body").on("submit", "form", function() {
        input = $(":submit", this);
        input.attr('disabled', true);
    });
});