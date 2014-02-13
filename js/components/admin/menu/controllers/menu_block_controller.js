$.Controller.extend('Menu_block',{
    defaults: {
        viewpath:'//components/admin/menu/views/'
    }

},{
    init:function(){

        this.elementId = this.element.attr('id');

        $('#menuBlockForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                role: {
                    required: true,
                },
                position: {
                    required: true,
                    maxlength: 2,
                    digits: true
                }
            },
            messages: {
                name: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                role: {
                    required: lang.validation.required
                },
                position: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength,
                    digits: lang.validation.digits
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

    '#menuBlockForm submit': function(el, ev){
        ev.preventDefault();

        if($(el).valid() === true) {

            Menu_block_model.set_menu_block($(el).serialize(), this.callback('menuBlockSaved'));

        }
    },

    menuBlockSaved: function(data){
        if (data.success) {

            if (data.message) {
                show_error(data.message);
                return;
            }

            $('#menu_block_id').val(data.data.id);

            show_success(lang.saved);

            $('#'+this.options.elementId).controller().loadMenuBlock();

        } else
            show_error(data.message ? data.message : lang.error);

    }

});