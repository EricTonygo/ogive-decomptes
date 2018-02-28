$(document).ready(function () {
    $('#edit_decompte').click(function(e){
        e.preventDefault();
        $('.form_field').show();
        $('.view_field').hide();
    });
    $('.cancel_edit_decompte').click(function(e){
        e.preventDefault();
        $('.form_field').hide();
        $('.view_field').show();
        
    });
});