$.Controller.extend('Integrator',{
    defaults: {
        viewpath:'//components/admin/integrators/views/'
    }
},{

    init:function(selector, data)
    {
        this.elementId = this.element.attr('id');
        this.counter = 0;

        if (this.elementId == '#setIntegrator_window') {
            this._validate();
        }/* else {
            this._autocomplete_placeholder();
        }*/
        if (data.data) {
            this.id = data.data[0].id;
            this.renderPlaceholders(data.data[0].placeholders.result);
        }
    },

    '#integratorForm submit': function(el, ev)
    {
        ev.preventDefault();
        var data;

        this._validate(el);

        if($(el).valid() == true)
        {
            data = $(el).serialize();
            Integrators_model.set_integrator(data, this.callback('integratorSaved'));
        }
    },

    renderPlaceholders: function(data)
    {
        $('.placeholderForms', '#' + this.elementId).empty();

        for (var i = 0, ln = data.length; i < ln; i++)
        {
            var html = $.View(this.Class.defaults.viewpath + 'all_placeholders.tmpl', {
                our_data: data[i]
            });
            $('.placeholderForms', '#' + this.elementId).append(html);
        }
    },

    integratorSaved: function(data)
    {
        if (data && data.message)
        {
            show_error(data.message);
            return;
        }

        if (data.success)
        {
            show_success(lang.saved);
            $('#id', this.elementId).val(data.data);
            this.publish('integratorSaved');

        } else if (data.message == false)
        {
            show_error(lang.error);
        }
    },

    '#addPlaceholder click': function()
    {
        this.addPlaceholder();
    },

    addPlaceholder: function ()
    {
        var html = $.View(this.Class.defaults.viewpath+'add_placeholder.tmpl', {
            counter: this.counter,
            integrator_id: this.id
        });
        $('#placeholderForms').prepend(html);

        this._autocomplete($('#placeholderName'+this.counter));
        this.counter++;
    },

    '.savePlaceholder click': function(el, ev)
    {
        ev.preventDefault();

        var $form = $(el).parents('.placeholderForm');
        this._validate($form);

        if ($form.valid() !== false) {
            Integrators_model.set_integrator_placeholder($form.serialize(), this.callback('placeholderSaved', $form));
        }
    },

    placeholderSaved: function(el, data)
    {
        if (data.success == true) {

            if (data.message)
            {
                show_error(data.message);

            } else
            {
                show_success(lang.integrator.savedPlaceholder);
                el.find('.formId').val(data.data);
                el.find('.productName').prop('disabled', true);
                el.find('.deletePlaceholder').children('span').removeClass('glyphicon-remove').addClass('glyphicon-trash');
                this.addPlaceholder();
            }

        } else
            show_error('Что-то пошло не так');
    },

    '.deletePlaceholder click': function(el)
    {
        var $el = $(el),
            $form = $el.parents('.placeholderForm');

        if ($el.children('span').hasClass('glyphicon-remove'))
        {
            $form.fadeOut(300, function(){
                $form.remove();
            });

        } else
        {
            if (confirm(lang.integrator.removeConfirm))
            {
                Integrators_model.delete_integrator({
                    placeholder_id: $form.find('.placeholderId').val(),
                    id: this.id,
                    delete_placeholder_relation_only: true
                }, this.callback('placeholderDeleted', $form));
            }
        }

    },

    placeholderDeleted: function($form, data) {

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

    },

    _validate: function(element)
    {
        $(element).validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255
                }
            },
            messages: {
                name: {
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

    _autocomplete: function(element)
    {
        element.autocomplete({
            serviceUrl: base_url + 'admin/placeholders/placeholder_autocomplete', // Страница для обработки запросов автозаполнения
            minChars: 3, // Минимальная длина запроса для срабатывания автозаполнения
            //            delimiter: /(,|;)\s*/, // Разделитель для нескольких запросов, символ или регулярное выражение
            maxHeight: 400, // Максимальная высота списка подсказок, в пикселях
            width: 300, // Ширина списка
            zIndex: 19999, // z-index списка
            deferRequestBy: 300, // Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
            onSelect: function(data, value, element) {
                $(element).next().val(value);
            } // Callback функция, срабатывающая на выбор одного из предложенных вариантов,
            //            lookup: ['January', 'February', 'March'] // Список вариантов для локального автозаполнения
        });
    }
});