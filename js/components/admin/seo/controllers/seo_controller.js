$.Controller.extend('Seo',{
    defaults: {
        viewpath:'//components/admin/seo/views/'
    }

},{
    init:function(selector){
        this.elementId = this.element.attr('id');

        Seo_model.get_seo(this.callback('seoGeted'));
    },

    /*
     * Get SEO
     */

    seoGeted: function(data){

        if (data && data.message) {
            show_error(data.message);
        }

        if (data && data.success){
            var html = $.View(this.Class.defaults.viewpath+'index.tmpl', {
                our_data: data ? data.data : data
            });

            this.element.html(html);
        } else if (data.message == false) {
            show_error(lang.error)
        }

        componentLoaded(this.element);
    },

    /*
     * Set SEO
     */

    'form.set_seo submit': function(el,ev){
        ev.preventDefault();

        this.seo_validate(el);

        if($(el).valid() === true) {
            Seo_model.set_seo($(el).serialize(), this.callback('seoSeted', el));
        }

    },

    seoSeted: function(el, data){

        if (data && data.message) {
            show_error(data.message);
        }

        if (data.success) {

            $(el).find('.seo_lang_id').val(data.data)
            show_success(lang.saved);
            
        } else {
            show_error(lang.error);
        }

    },

    seo_validate: function(element){

        $(element).validate({
            rules: {
                title: {
                    required: true,
                    minlength: 5,
                    maxlength: 500
                },
                description: {
                    required: true,
                    minlength: 20,
                    maxlength: 1000
                },
                key_words: {
                    required: true,
                    minlength: 5,
                    maxlength: 500
                }
            },
            messages: {
                title: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                description: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                key_words: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
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

});