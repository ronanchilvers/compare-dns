$(function (){
    $('#upload-form input[type=file]').change(function () {
        var files = $(this).prop('files');
        if (!!files[0]) {
            $(this).siblings('.file-name').text(files[0].name);
        }
    });
});
