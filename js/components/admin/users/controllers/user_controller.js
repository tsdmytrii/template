$.Controller('User_widget', {}, {

    init: function(element, options) {
        if (typeof this.options.full_functionality == 'undefined') {
            this.options.full_functionality = false;
        }
        //alert($.dump(this.options));
        $('#set_user_form').validate({
            rules: {
                username: {
                    minlength: 3,
                    required: true
                },
                email: {
                    required: true,
                    unique_email: true,
                    email: true
                            //remote:base_url+'admin/users/check_email'
                },
                password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    equalTo: '#password'
                },
                'name': {
                    required: true,
                    maxlength: 40,
                    minlength: 3
                },
                surname: {
                    required: true,
                    maxlength: 40,
                    minlength: 3
                },
                address: {
                    required: true,
                    maxlength: 100,
                    minlength: 3
                },
                home_numb: {
                    required: true,
                    maxlength: 4
                },
                index: {
                    required: true,
                    maxlength: 7,
                    minlength: 3
                },
                city: {
                    required: true,
                    maxlength: 40
                }
            },
            messages: {

                username: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength
                },
                email: {
                    required: lang.validation.required,
                    unique_email: lang.validation.unique_email,
                    email: lang.validation.email
                            //remote:base_url+'admin/users/check_email'
                },
                password: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength
                },
                confirm_password: {
                    required: lang.validation.required,
                    equalTo: lang.validation.equal_to_pass
                },
                'name': {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                surname: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                address: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                home_numb: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                },
                index: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                city: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                }
            },
            highlight: function(element){
                $(element).parents('.form-group').addClass('has-error');
            },
            unhighlight: function(element){
                $(element).parents('.form-group').removeClass('has-error').find('.help').empty();
            },
            errorPlacement: function(error, element){
                element.parents('.form-group').find('.help').append(error).animate({
                    opacity: "1"
                }, 1000);
            }
        });
        if ($('.user_id').val().length > 0 && $('.user_id').val() == parseInt($('.user_id').val())) {

            if (parseInt(this.options.data.group.removed) == 0)
                $('.allGroups', '#set_user_form').remove();
            //removing required on password
            $('#password').rules('remove', 'required');
            $('#confirm_password').rules('remove', 'required');

        }
    },

    '#set_user_form submit': function(el, ev) {
        ev.preventDefault();

        if (el.valid() !== false) {
            UserModel.setUser(el.serialize(), this.callback('user_saved'));
        }
    },

    user_saved: function(response) {

        if (response && response.message) {
            show_error(response.message);
        }

        if (response && response.success) {

            if (response.data == parseInt(response.data)) {
                $('.user_id').val(response.data);
            }

            if (this.options.full_functionality !== false) {

                $('#'+this.options.elementId).controller().loadUsers();

            }

            $('.allGroups', '#set_user_form').show();

            show_success('User saved successfully');

        } else if (response.message == false) {

            show_error(lang.error);

        }
    }

});