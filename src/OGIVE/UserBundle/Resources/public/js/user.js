$(function () {
    $('#login_form.ui.form')
            .form({
                fields: {
                    _username: {
                        identifier: '_username',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Veuillez votre email ou pseudo'
                            }
                        ]
                    },
                    _password: {
                        identifier: '_password',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Veuillez saisir votre mot de passe'
                            }
                        ]
                    }
                },
                inline: true,
                on: 'blur'
                       
            }
            );

    $('#register_form.ui.form')
            .form({
                fields: {
                    lastname: {
                        identifier: 'lastname',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Enter your owner's name"
                            }
                        ]
                    },
                    username: {
                        identifier: 'username',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Enter your username"
                            }
                        ]
                    },
                    email: {
                        identifier: 'email',
                        rules: [
                            {
                                type: 'email',
                                prompt: 'Enter your email'
                            }
                        ]
                    },
                    password: {
                        identifier: 'password',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Enter your password'
                            }
                        ]
                    },
                    passwordConfirm: {
                        identifier: 'passwordConfirm',
                        rules: [
                            {
                                type: 'match[password]',
                                prompt: 'The passwords you have entered do not match'
                            }
                        ]
                    },
                    termsConditions: {
                        identifier: 'termsConditions',
                        rules: [
                            {
                                type: 'checked',
                                prompt: 'You must accept the terms and conditions'
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit'
            }
            );
    
    $('#submit_register_form').click(function (e) {
        if ($('#register_form').form('is valid')) {
//            $('#register_account_form').addClass('loading');
            $('#submit_register_form').addClass('loading');
        }
    });
    
    $('#submit_login_form').click(function (e) {
        if ($('#login_form').form('is valid')) {
//            $('#login_account_form').addClass('loading');
            $('#submit_login_form').addClass('loading');
        }
    });

    $('#update_profile_btn').click(function(e){
        e.preventDefault();
        $('#show_profile').hide();
        $('#update_profile').show();
    });
    
    $('#show_profile_btn').click(function(e){
        e.preventDefault();
        $('#update_profile').hide();
        $('#show_profile').show();
    });
});