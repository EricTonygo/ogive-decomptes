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
    $('input[data-validate="lot_startDate"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });
    $('input[data-validate="lot_endDate"]').datetimepicker({
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