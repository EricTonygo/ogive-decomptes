$(document).ready(function () {
    $('#od_add_project_submit_btn').click(function (e) {
        e.preventDefault();
        $('#form_message_error').hide();
        $('#form_message_success').hide();
        $('#od_add_project_form.ui.form').submit();
    });
    $('#od_add_project_form.ui.form')
            .form({
                fields: {
                    numero_marche: {
                        identifier: 'numeroMarche',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le numéro du marché"
                            }
                        ]
                    },
                    subject: {
                        identifier: 'subject',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir l'objet du projet"
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'POST',
                        url: $('#od_add_project_form.ui.form').attr('action'),
                        data: $('#od_add_project_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#od_add_project_submit_btn').addClass('disabled');
                            $('#od_add_project_cancel_btn').addClass('disabled');
                            $('#od_add_project_form.ui.form').addClass('loading');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                $('#form_message_error').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#od_add_project_submit_btn').removeClass('disabled');
                                    $('#od_add_project_cancel_btn').removeClass('disabled');
                                    $('#od_add_project_form.ui.form').removeClass('loading');
                                    $('#form_message_error span').html(myerrors.message);
                                    $('#form_message_error').show();
                                } else {
                                    $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                    $('#form_message_error').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#od_add_project_form.ui.form').removeClass('loading');
                            $('#form_message_success span').html(response.message);
                            $('#form_message_sucess').show();
                            var id_project = parseInt(response.id_project);
                            window.location.replace(Routing.generate('project_contributors_get', {id: id_project}));
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#od_add_project_submit_btn').removeClass('disabled');
                            $('#od_add_project_cancel_btn').removeClass('disabled');
                            $('#od_add_project_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );

    $('#od_update_project_submit_btn').click(function (e) {
        e.preventDefault();
        $('#form_message_error').hide();
        $('#form_message_success').hide();
        $('#od_update_project_form.ui.form').submit();
    });
    $('#od_update_project_form.ui.form')
            .form({
                fields: {
                    numero_marche: {
                        identifier: 'numeroMarche',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le numéro du marché"
                            }
                        ]
                    },
                    subject: {
                        identifier: 'subject',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir l'objet du projet"
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'PUT',
                        url: $('#od_update_project_form.ui.form').attr('action'),
                        data: $('#od_update_project_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#od_update_project_submit_btn').addClass('disabled');
                            $('#od_update_project_cancel_btn').addClass('disabled');
                            $('#od_update_project_form.ui.form').addClass('loading');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                $('#form_message_error').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#od_update_project_submit_btn').removeClass('disabled');
                                    $('#od_update_project_cancel_btn').removeClass('disabled');
                                    $('#od_update_project_form.ui.form').removeClass('loading');
                                    $('#form_message_error span').html(myerrors.message);
                                    $('#form_message_error').show();

                                } else {
                                    $('#form_message_error span').html("Une erreur est survenue lors de la modification");
                                    $('#form_message_error').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#od_update_project_form.ui.form').removeClass('loading');
                            $('#form_message_success span').html(response.message);
                            $('#form_message_success').show();
                            var id_project = parseInt(response.id_project);
                            window.location.replace(Routing.generate('project_gen_infos_get', {id: id_project}));
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#od_update_project_submit_btn').removeClass('disabled');
                            $('#od_update_project_cancel_btn').removeClass('disabled');
                            $('#od_update_project_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );
});

function delete_project(idProject, id) {
    $('#confirm_delete_project.ui.small.modal')
            .modal('show');

    $('#execute_delete_project').click(function (e) {
        e.preventDefault();
        $('#confirm_delete_project.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'DELETE',
            url: Routing.generate('project_delete', {idProject: idProject, id: id}),
            dataType: 'json',
            beforeSend: function () {
                $("#delete_project_btn" + id).addClass("loading");
            },
            statusCode: {
                500: function (xhr) {
                    $('#message_error span').html("Erreur s'est produite au niveau du serveur");
                    $('#message_error').show();

                },
                404: function (response, textStatus, jqXHR) {
                    $('#message_error span').html(response.responseJSON.message);
                    $('#message_error').show();

                }
            },
            success: function (response, textStatus, jqXHR) {
                $('tr#project' + id).remove();
                $('#message_loading').hide();
                $('#message_success span').html(response.message);
                $('#message_success').show();
                //window.location.replace(Routing.generate('call_offer_index'));
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#delete_project_btn" + id).removeClass("loading");
            }
        });
    });
}