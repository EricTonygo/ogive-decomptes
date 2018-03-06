$(document).ready(function () {
    
});


function delete_holder(idProject, id) {
    $('#confirm_delete_holder.ui.small.modal')
            .modal('show');

    $('#execute_delete_holder').click(function (e) {
        e.preventDefault();
        $('#confirm_delete_holder.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'DELETE',
            url: Routing.generate('holder_delete', {idProject: idProject, id: id}),
            dataType: 'json',
            beforeSend: function () {
                $("#delete_holder_btn" + id).addClass("loading");
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
                $('tr#holder' + id).remove();
                $('#message_loading').hide();
                $('#message_success span').html(response.message);
                $('#message_success').show();
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#delete_holder_btn" + id).removeClass("loading");
            }
        });
    });
}

function delete_project_manager(idProject, id) {
    $('#confirm_delete_project_manager.ui.small.modal')
            .modal('show');

    $('#execute_delete_project_manager').click(function (e) {
        e.preventDefault();
        $('#confirm_delete_project_manager.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'DELETE',
            url: Routing.generate('project_manager_delete', {idProject: idProject, id: id}),
            dataType: 'json',
            beforeSend: function () {
                $("#delete_project_manager_btn" + id).addClass("loading");
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
                $('tr#project_manager' + id).remove();
                $('#message_loading').hide();
                $('#message_success span').html(response.message);
                $('#message_success').show();
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#delete_project_manager_btn" + id).removeClass("loading");
            }
        });
    });
}

function delete_service_provider(idProject, id) {
    $('#confirm_delete_service_provider.ui.small.modal')
            .modal('show');

    $('#execute_delete_service_provider').click(function (e) {
        e.preventDefault();
        $('#confirm_delete_service_provider.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'DELETE',
            url: Routing.generate('service_provider_delete', {idProject: idProject, id: id}),
            dataType: 'json',
            beforeSend: function () {
                $("#delete_service_provider_btn" + id).addClass("loading");
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
                $('tr#service_provider' + id).remove();
                $('#message_loading').hide();
                $('#message_success span').html(response.message);
                $('#message_success').show();
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#delete_service_provider_btn" + id).removeClass("loading");
            }
        });
    });
}

function delete_other_contributor(idProject, id) {
    $('#confirm_delete_other_contributor.ui.small.modal')
            .modal('show');

    $('#execute_delete_other_contributor').click(function (e) {
        e.preventDefault();
        $('#confirm_delete_other_contributor.ui.small.modal')
                .modal('hide');
        $('#message_error').hide();
        $('#message_success').hide();
        $.ajax({
            type: 'DELETE',
            url: Routing.generate('other_contributor_delete', {idProject: idProject, id: id}),
            dataType: 'json',
            beforeSend: function () {
                $("#delete_other_contributor_btn" + id).addClass("loading");
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
                $('tr#other_contributor' + id).remove();
                $('#message_loading').hide();
                $('#message_success span').html(response.message);
                $('#message_success').show();
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#delete_other_contributor_btn" + id).removeClass("loading");
            }
        });
    });
}