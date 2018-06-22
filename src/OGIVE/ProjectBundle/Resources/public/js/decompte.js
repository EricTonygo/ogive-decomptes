$(document).ready(function () {
    $('#edit_decompte').click(function (e) {
        e.preventDefault();
        $('.form_field').show();
        $('.view_field').hide();
    });
    $('.cancel_edit_decompte').click(function (e) {
        e.preventDefault();
        $('.form_field').hide();
        $('.view_field').show();

    });
    $('.select_another_decompte').change(function (e) {
        e.preventDefault();
        var url = $(this).find('option:selected').val();
        if (url !== "") {
            window.location.replace(url);
        }
    });
    $('#od_add_decompte_submit_btn').click(function (e) {
        e.preventDefault();
        $('#form_message_error').hide();
        $('#form_message_success').hide();
        $('#od_add_decompte_form.ui.form').submit();
    });
    $('#od_add_decompte_form.ui.form')
            .form({
                fields: {
                    month_name: {
                        identifier: 'monthName',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le ou les nom(s) des mois"
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'POST',
                        url: $('#od_add_decompte_form.ui.form').attr('action'),
                        data: $('#od_add_decompte_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#od_add_decompte_submit_btn').addClass('disabled');
                            $('#od_add_decompte_cancel_btn').addClass('disabled');
                            $('#od_add_decompte_form.ui.form').addClass('loading');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                $('#form_message_error').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#od_add_decompte_submit_btn').removeClass('disabled');
                                    $('#od_add_decompte_cancel_btn').removeClass('disabled');
                                    $('#od_add_decompte_form.ui.form').removeClass('loading');
                                    $('#form_message_error span').html(myerrors.message);
                                    $('#form_message_error').show();
                                } else {
                                    $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                    $('#form_message_error').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#od_add_decompte_form.ui.form').removeClass('loading');
                            $('#form_message_success span').html(response.message);
                            $('#form_message_sucess').show();
                            var id_project = parseInt(response.id_project);
                            var id_decompte = parseInt(response.id_decompte);
                            window.location.replace(Routing.generate('decompte_update_get', {idProject: id_project, id: id_decompte}));
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#od_add_decompte_submit_btn').removeClass('disabled');
                            $('#od_add_decompte_cancel_btn').removeClass('disabled');
                            $('#od_add_decompte_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );

    $('#od_update_decompte_submit_btn').click(function (e) {
        e.preventDefault();
        $('#form_message_error').hide();
        $('#form_message_success').hide();
        $('#od_update_decompte_form.ui.form').submit();
    });
    $('#od_update_decompte_form.ui.form')
            .form({
                fields: {
                    month_name: {
                        identifier: 'monthName',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le ou les nom(s) des mois"
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'PUT',
                        url: $('#od_update_decompte_form.ui.form').attr('action'),
                        data: $('#od_update_decompte_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#od_update_decompte_submit_btn').addClass('disabled');
                            $('#od_update_decompte_cancel_btn').addClass('disabled');
                            $('#od_update_decompte_form.ui.form').addClass('loading');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                $('#form_message_error').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#od_update_decompte_submit_btn').removeClass('disabled');
                                    $('#od_update_decompte_cancel_btn').removeClass('disabled');
                                    $('#od_update_decompte_form.ui.form').removeClass('loading');
                                    $('#form_message_error span').html(myerrors.message);
                                    $('#form_message_error').show();

                                } else {
                                    $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                    $('#form_message_error').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#od_update_decompte_form.ui.form').removeClass('loading');
                            $('#form_message_success span').html(response.message);
                            $('#form_message_success').show();
                            var id_project = parseInt(response.id_project);
                            var id_decompte = parseInt(response.id_decompte);
                            window.location.replace(Routing.generate('decompte_show_get', {idProject: id_project, id: id_decompte}));
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#od_update_decompte_submit_btn').removeClass('disabled');
                            $('#od_update_decompte_cancel_btn').removeClass('disabled');
                            $('#od_update_decompte_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );

});


function delete_decompte(idProject, id) {
    $('#confirm_delete_decompte.ui.small.modal')
            .modal('show');

    $('#execute_delete_decompte').click(function (e) {
        e.preventDefault();
        $('#confirm_delete_decompte.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'DELETE',
            url: Routing.generate('decompte_delete', {idProject: idProject, id: id}),
            dataType: 'json',
            beforeSend: function () {
                $("#delete_decompte_btn" + id).addClass("loading");
            },
            statusCode: {
                500: function (xhr) {
                    $('#message_error span').html("Erreur s'est produite au niveau du serveur");
                    $('#message_error').show();

                },
                404: function (response, textStatus, jqXHR) {
                    $('#message_error span').html(response.responseJSON.message);
                    $('#message_error').show();

                },
                400: function (response, textStatus, jqXHR) {
                    $('#message_error span').html(response.responseJSON.message);
                    $('#message_error').show();
                }
            },
            success: function (response, textStatus, jqXHR) {
                $('tr#decompte' + id).remove();
                $('#message_loading').hide();
                $('#message_success span').html(response.message);
                $('#message_success').show();
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#delete_decompte_btn" + id).removeClass("loading");
            }
        });
    });


}


function validate_decompte(id) {
    $('#validate_decompte.ui.small.modal')
            .modal('show');
//    if($('#div_checkbox'+id).checkbox('is checked')){
//        $('#div_checkbox'+id).checkbox('uncheck');
//    }
    $('#execute_validate_decompte').click(function (e) {
        e.preventDefault();
        $('#validate_decompte.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'PUT',
            data: $('#validate_decompte_form.ui.form').serialize(),
            url: Routing.generate('decompte_validation_post', {id: id}),
            dataType: 'json',
            beforeSend: function () {
                $('#div_checkbox'+id).hide();
            },
            statusCode: {
                500: function (xhr) {
                    $('#message_error span').html("Erreur s'est produite au niveau du serveur");
                    $('#message_error').show();

                },
                404: function (response, textStatus, jqXHR) {
                    $('#message_error span').html(response.responseJSON.message);
                    $('#message_error').show();

                },
                400: function (response, textStatus, jqXHR) {
                    $('#message_error span').html(response.responseJSON.message);
                    $('#message_error').show();
                }
            },
            success: function (response, textStatus, jqXHR) {
                $('#div_checkbox'+id).checkbox('check');
                window.location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                //$("#delete_decompte_btn" + id).removeClass("loading");
            }
        });
    });
}


function submit_decompte_for_validation(idProject, id) {
    $('#confirm_submission_decompte.ui.small.modal')
            .modal('show');

    $('#execute_submission_decompte').click(function (e) {
        e.preventDefault();
        $('#confirm_submission_decompte.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'POST',
            url: Routing.generate('decompte_submit_for_validation_post', {idProject: idProject, id: id}),
            dataType: 'json',
            beforeSend: function () {
                $("#submit_decompte").addClass("loading");
            },
            statusCode: {
                500: function (xhr) {
                    $('#message_error span').html("Erreur s'est produite au niveau du serveur");
                    $('#message_error').show();

                },
                404: function (response, textStatus, jqXHR) {
                    $('#message_error span').html(response.responseJSON.message);
                    $('#message_error').show();
                },
                400: function (response, textStatus, jqXHR) {
                    $('#message_error span').html(response.responseJSON.message);
                    $('#message_error').show();
                }
            },
            success: function (response, textStatus, jqXHR) {
                $("#submit_decompte").removeClass("loading");
                $('#message_success span').html(response.message);
                $('#message_success').show();
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#submit_decompte").removeClass("loading");
            }
        });
    });


}