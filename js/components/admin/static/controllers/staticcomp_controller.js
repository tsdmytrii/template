$.Controller.extend('Staticcomp',{
    defaults: {
        viewpath:'//components/admin/static/views/'
    }

},{
    init:function(selector, content_id, component_type_id) {

        this.component_type_id = component_type_id;
        this.id = content_id;
        this.elementId = '#'+this.element.attr('id');

        Static_model.get_static({
            static_component_id: this.id
        }, this.callback('staticGeted'));

    },

    /*
     * GET functions
     */

    staticGeted: function(data) {

        if (data.message) {
            show_error(data.message);
        }

        if (data && data.success) {

            var html = $.View(this.Class.defaults.viewpath+'index.tmpl', {
                our_data: data.data,
                content_id: this.id,
                element_id: this.elementId
            });

            $(this.elementId).html(html);
            imperavi($('.content_desc'));
            date_time_picker($('#date'));

        } else if (data.message == false) {

            show_error(lang.error);

        }

        componentLoaded(this.element);

    },

    /*
     * SET functions
     */

    'form.static_comp_form submit': function(el, ev) {
        ev.preventDefault();
        var data,
            $dataForm = $('#staticDataForm');

        this._data_validate();
        this._validate($(el));

        if($(el).valid() == true && $dataForm.valid()) {

            data = $(el).serialize()+'&component_type_id='+this.component_type_id+'&'+$dataForm.serialize();
            Static_model.set_static_component(data, this.callback('staticSaved', el));

        }
    },

    staticSaved: function(el, data) {

        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {

            show_success(lang.saved);

            if (data.data.link_id)
                $(el).find('.link_id').val(data.data.link_id);

        } else if (data.message == false) {
            show_error(lang.error);
        }
    },

    /*
     * HELPER functions
     */

    _data_validate: function() {

        $('#staticDataForm', this.elementId).validate({
            rules: {
                date: {
                    required: true,
                    minlength: 10,
                    maxlength: 20
                }
            },
            messages: {
                date: {
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

    },

    _validate: function(element) {

        element.validate({
            rules: {
                title: {
                    required: true,
                    minlength: 5,
                    maxlength: 500
                },
                author: {
                    required: true,
                    minlength: 5,
                    maxlength: 100
                },
                description: {
                    required: true,
                    minlength: 20,
                    maxlength: 12000
                },
                link: {
                    regexp: '^[a-zA-Z0-9_]+$',
                    maxlength: 100,
                    unique: true
                },
                key_words: {
                    maxlength: 1000
                },
                seo_description: {
                    maxlength: 4000
                },
                seo_title: {
                    maxlength: 500
                }
            },
            messages: {
                title: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                author: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                description: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                link: {
                    regexp: lang.validation.regexp,
                    maxlength: lang.validation.maxlength,
                    unique: lang.validation.unique_link
                },
                key_words: {
                    maxlength: lang.validation.maxlength
                },
                seo_description: {
                    maxlength: lang.validation.maxlength
                },
                seo_title: {
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