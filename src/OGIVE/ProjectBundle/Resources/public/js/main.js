$(document).ready(function () {
    $('.ui.dropdown')
            .dropdown()
            ;

    $('.ui.sidebar')
            .sidebar({
                context: $('.bottom.segment.egis_app_content'),
                dimPage: false,
                closable: true,
                scrollLock: true
            })
            .sidebar('setting', 'transition', 'push')
            .sidebar('attach events', '.egis_toc.item')
            ;

//    $('.ui.sidebar').click(function(){
//        if($(this).sidebar("isvisible"))
//        $('.ui.sidebar').sidebar("show");
//    });
    $('input[name="birth-date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y H:i',
        lang: 'fr',
        scrollInput: false
    });
    $('input[name="hiring-date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y H:i',
        lang: 'fr',
        scrollInput: false
    });
    $('input[name="start-date"]').datetimepicker({
        //timepicker: true,
        //minDate: '0',
        format: 'd-m-Y H:i',
        lang: 'fr',
        scrollInput: false
    });
    $('input[name="end-date"]').datetimepicker({
        //timepicker: true,
        //minDate: '0',
        format: 'd-m-Y H:i',
        lang: 'fr',
        scrollInput: false
    });
});