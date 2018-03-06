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

    $('.message .close')
            .on('click', function () {
                $(this).parent(".message").hide();
            })
            ;

//    $('.ui.sidebar').click(function(){
//        if($(this).sidebar("isvisible"))
//        $('.ui.sidebar').sidebar("show");
//    });
    $('input[data-validate="start_date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });
    $('input[data-validate="end_date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });
    $('input[data-validate="suscription_date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });
    $('input[data-validate="signature_date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });

    $('input[data-validate="notification_date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });

    $('input[name="start-date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });
    $('input[name="end-date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });

    $('.od_decompte_content .menu .item')
            .tab({
                context: '.od_decompte_content'
            })
            ;

});