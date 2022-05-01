$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });
    $.getScript("./website/functions.js", function() {
        bind(true);
    });
});