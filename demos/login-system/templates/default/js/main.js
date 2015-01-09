/*** assign action to all close buttons */
$(function() {
    $(".close").click(function() {
        $(this).parent().hide();
    });
});