$(document).ready(function () {
    $('.ui.dropdown')
            .dropdown()
            ;

    $('.ui.sidebar')
            .sidebar({
                //context: $('.bottom.segment'),
                dimPage: false
            })
            .sidebar('setting', 'transition', 'overlay')
            .sidebar('attach events', '.toc.item')
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
    $('input[data-validate="validation_date"]').datetimepicker({
        timepicker: false,
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });
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
    $('input[data-validate="suscription-date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });
    $('input[data-validate="signature-date"]').datetimepicker({
        timepicker: false,
        //minDate: '0',
        format: 'd-m-Y',
        lang: 'fr',
        scrollInput: false
    });

    $('input[data-validate="notification-date"]').datetimepicker({
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

    $('.right_mini_card_project_first_content .menu .item')
            .tab({
                context: '.right_mini_card_project_second_content'
            })
            ;
            
    $('.home_tab .menu .item')
            .tab()
            ;

});