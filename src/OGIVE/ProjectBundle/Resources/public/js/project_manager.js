$(document).ready(function () {
    $('#od_add_project_manager_submit_btn').click(function (e) {
        e.preventDefault();
        $('#form_message_error').hide();
        $('#form_message_success').hide();
        $('#od_add_project_manager_form.ui.form').submit();
    });
    $('#od_add_project_manager_form.ui.form')
            .form({
                fields: {
                    name: {
                        identifier: 'nom',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir son nom"
                            }
                        ]
                    },
                    email: {
                        identifier: 'email',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir son adresse email"
                            }
                        ]
                    },
                    phone: {
                        identifier: 'phone',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir son numéro de téléphone"
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'POST',
                        url: $('#od_add_project_manager_form.ui.form').attr('action'),
                        data: $('#od_add_project_manager_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#od_add_project_manager_submit_btn').addClass('disabled');
                            $('#od_add_project_manager_cancel_btn').addClass('disabled');
                            $('#od_add_project_manager_form.ui.form').addClass('loading');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                $('#form_message_error').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#od_add_project_manager_submit_btn').removeClass('disabled');
                                    $('#od_add_project_manager_cancel_btn').removeClass('disabled');
                                    $('#od_add_project_manager_form.ui.form').removeClass('loading');
                                    $('#form_message_error span').html(myerrors.message);
                                    $('#form_message_error').show();
                                } else {
                                    $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                    $('#form_message_error').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#od_add_project_manager_form.ui.form').removeClass('loading');
                            $('#form_message_success span').html(response.message);
                            $('#form_message_sucess').show();
                            var id_project = parseInt(response.id_project);
                            window.location.replace(Routing.generate('project_project_managers_get', {id: id_project}));
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#od_add_project_manager_submit_btn').removeClass('disabled');
                            $('#od_add_project_manager_cancel_btn').removeClass('disabled');
                            $('#od_add_project_manager_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );

    $('#od_update_project_manager_submit_btn').click(function (e) {
        e.preventDefault();
        $('#form_message_error').hide();
        $('#form_message_success').hide();
        $('#od_update_project_manager_form.ui.form').submit();
    });
    $('#od_update_project_manager_form.ui.form')
            .form({
                fields: {
                    name: {
                        identifier: 'nom',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir son nom"
                            }
                        ]
                    },
                    email: {
                        identifier: 'email',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir son adresse email"
                            }
                        ]
                    },
                    phone: {
                        identifier: 'phone',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir son numéro de téléphone"
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'PUT',
                        url: $('#od_update_project_manager_form.ui.form').attr('action'),
                        data: $('#od_update_project_manager_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#od_update_project_manager_submit_btn').addClass('disabled');
                            $('#od_update_project_manager_cancel_btn').addClass('disabled');
                            $('#od_update_project_manager_form.ui.form').addClass('loading');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                $('#form_message_error').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#od_update_project_manager_submit_btn').removeClass('disabled');
                                    $('#od_update_project_manager_cancel_btn').removeClass('disabled');
                                    $('#od_update_project_manager_form.ui.form').removeClass('loading');
                                    $('#form_message_error span').html(myerrors.message);
                                    $('#form_message_error').show();

                                } else {
                                    $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                    $('#form_message_error').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#od_update_project_manager_form.ui.form').removeClass('loading');
                            $('#form_message_success span').html(response.message);
                            $('#form_message_success').show();
                            var id_project = parseInt(response.id_project);
                            window.location.replace(Routing.generate('project_project_managers_get', {id: id_project}));
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#od_update_project_manager_submit_btn').removeClass('disabled');
                            $('#od_update_project_manager_cancel_btn').removeClass('disabled');
                            $('#od_update_project_manager_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );
});