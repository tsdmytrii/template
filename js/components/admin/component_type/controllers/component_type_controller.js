$.Controller.extend('Component_type',{
    defaults: {
        viewpath:'//components/admin/component_type/views/',

        lang_id: 2,
        pref: 'ru'
    }

},{
    init:function(selector){

        this.elementId = this.element.attr('id');

        this.component_type_id = 0;

        if (this.options.data) {
            $('.compTypeTab').removeClass('disabled');

            this.component_type_id = this.options.data.id;

            this.getFunctions();
        }

        $('#componentTypeForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                psevdo_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                library: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                admin_client_controller: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                client_controller: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                }
            },
            messages: {
                name: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                psevdo_name: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                library: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                admin_client_controller: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                client_controller: {
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

    '#componentTypeForm submit': function(el,ev) {
        ev.preventDefault();

        if($(el).valid() === true) {

            Component_type_model.set_component_type($(el).serialize(), this.callback('componentTypeSaved', el));

        }

    },

    componentTypeSaved: function(el, data){

        if (data.message) {
            show_error(data.message);
            return;
        }

        if (data.success == true) {

            show_success(lang.saved);

            if (this.component_type_id === 0) {

                this.component_type_id = data.data;

                $('#component_type_id').val(data.data);

                this.getFunctions();

            }

            $('.compTypeTab').removeClass('disabled');

            $('#'+this.options.elementId).controller().loadComponentTypes();

        } else if (data.message) {
            show_error(lang.error);
        }
    },

    /*
     * Component functions
     */

     getFunctions: function() {

         Component_type_model.get_all_component_function({
             component_type_id: this.component_type_id
         }, this.callback('functionsGeted'));

     },

     functionsGeted: function(data) {

        if (data.message) {

            show_error(data.message);
            return;

        } else {

            $('.function_forms', '#' + this.elementId).empty();

            for (var i = 0, ln = data.data.length; i < ln; i++) {
                var html = $.View(this.Class.defaults.viewpath + 'set_function.tmpl', {
                        component_type_id: this.component_type_id,
                        our_data: data.data[i]
                    });

                $('.function_forms', '#' + this.elementId).append(html);
            }
        }

    },

     '#addFunction click': function() {

         var html = $.View(this.Class.defaults.viewpath+'set_function.tmpl', {
                our_data: false,
                component_type_id: this.component_type_id
            });

         $('.function_forms', '#' + this.elementId).prepend(html);

     },

     '.saveFunction click': function(el, ev){
        ev.preventDefault();

        var $form = $(el).parents('.functionForm');

        this.functionValidate($form);

        if ($form.valid() !== false) {
            Component_type_model.set_component_function($form.serialize(), this.callback('functionSaved', $form));
        }

     },

     functionSaved: function(el, data) {
        if (data.success == true) {

            if (data.message) {

                show_error(data.message);

            } else {

                show_success('Функция сохранена!');

                el.find('.formId').val(data.data);

                el.find('.deleteFunction').children('span').removeClass('glyphicon-remove').addClass('glyphicon-trash');

            }

        } else
            show_error('Что-то пошло не так');
     },

     functionValidate: function(el) {

        $(el).validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                clear_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                }
            },
            messages: {
                name: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                clear_name: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                }
            },
            highlight: function(element) {
                $(element).parents('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).parents('.form-group').removeClass('has-error').find('.help').empty();
            },
            errorPlacement: function(error, element) {
                element.parents('.form-group').find('.help').append(error).animate({
                    opacity: "1"
                }, 1000);
            }
        });

     },

     '.deleteFunction click': function(el) {

         var $el = $(el),
             $form = $el.parents('.functionForm');

        if ($el.children('span').hasClass('glyphicon-remove')) {

            $form.fadeOut(300, function(){
                $form.remove();
            });

        } else {
            if (confirm("Вы действительно хотите удалить функцию?")) {

                Component_type_model.delete_component_function({component_function_id: $form.find('.formId').val()}, this.callback('functionDeleted', $form));

            }
        }

     },

     functionDeleted: function($form, data) {

        if (data.success == true) {

            if (data.message) {

                show_error(data.message);

            } else {

                show_success('Функция удалена!');

                $form.fadeOut(300, function() {
                    $form.remove();
                });
            }

        } else
            show_error('Что-то пошло не так!');

    }

});