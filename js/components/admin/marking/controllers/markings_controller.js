$.Controller.extend('Markings',{
    defaults: {
        viewpath:'//components/admin/marking/views/'
    }

},{
    init:function(selector, content_id) {

        this.elementId = $('.'+this.Class.fullName.toLowerCase()).attr('id');

        Marking_model.get_marking(this.callback('markingGeted'));

    },

    markingGeted: function(data){
        if (data && data.message) {
            show_error(data.message);
        }

        if (data && data.success) {
            var html = $.View(this.Class.defaults.viewpath+'index.tmpl', {
                marking: data ? data.data : data,
            });

            this.element.html(html);
        } else if (data && data.message == false) {
            show_error(lang.error);
        }

        componentLoaded(this.element);

    },

    '#markingForm submit': function(el,ev){
        ev.preventDefault();

        this.markingValidate();

        if($(el).valid() === true) {
            Marking_model.set_marking($(el).serialize(), this.callback('markingSeted'));
        }
    },

    markingSeted: function(data){
        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {
            show_success(lang.saved);
        } else {
            show_error(lang.error);
        }
    },

    markingValidate: function(){

        $('#markingForm').validate({
            rules: {
                min_width: {
                    required: true,
                    maxlength: 11
                },
                max_width: {
                    required: true,
                    maxlength: 11
                },
                width: {
                    required: true,
                    maxlength: 11
                },
                height: {
                    required: true,
                    maxlength: 11
                },
                min_font_size: {
                    required: true,
                    maxlength: 11
                }
            },
            messages: {
                min_width: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                },
                max_width: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                },
                width: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                },
                height: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                },
                min_font_size: {
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

    }
}
);