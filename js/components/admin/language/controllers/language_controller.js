$.Controller.extend('Language',{
    defaults: {
        viewpath:'//components/admin/language/views/'
    }

},{

    init: function(element, options) {

        this.elementId = this.element.attr('id');

        if(typeof this.options.full_functionality == 'undefined'){
            this.options.full_functionality = false;
        }

        $('.languageForm').validate({
            rules:{
                name:{
                    minlength:2,
                    maxlength: 200,
                    required:true
                },
                iso_code:{
                    maxlength: 3,
                    required:true
                },
                position:{
                    maxlength: 2,
                    required:true
                }
            },
            messages: {
                name: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength,
                    minlength: lang.validation.minlength
                },
                iso_code:{
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength,
                },
                position:{
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength,
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

    },

    '.languageForm submit': function(el, ev) {
        ev.preventDefault();

        if(el.valid() !== false){
            Language_model.set_language(el.serialize(), this.callback('language_saved'));
        }
    },

    language_saved: function(response) {

        if (response && response.message) {
            show_error(response.message);
        }


        if(response.success == true){

            $('.language_id', '#'+this.elementId).val(response.data);

            if(this.options.full_functionality !== false){
                $(this.options.language_wrap).controller().load_languages();
            }

            show_success(lang.saved);

        } else if (response.message == false){

            show_error(lang.error);

        }
    }

});