$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });
    $.getScript("http://127.0.0.1/strony/repo/kowaluki_repository/website/functions.js", function() {
        bind(true);
    });
});