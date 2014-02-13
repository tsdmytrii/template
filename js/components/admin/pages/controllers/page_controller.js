$.Controller.extend('Page',{
    defaults: {
        viewpath:'//components/admin/pages/views/'
    }
},{

    init:function() {

        this.elementId = '#'+this.element.attr('id');

        if (this.elementId == '#set_component_window') {
            this._validate();
        } else {
            this._autocomplete_menu_item();
        }

    },

    '.set_comp_form submit': function(el, ev){
        ev.preventDefault();
        var data;

        this._validate(el);

        if($(el).valid() == true) {
            data = $(el).serialize();

            Pages_model.set_component(data, this.callback('componentSaved'));
        }
    },

    componentSaved: function(data){

        if (data && data.message) {
            show_error(data.message);
            return;
        }

        if (data.success) {

            show_success(lang.saved);

            $('#component_id', this.elementId).val(data.data)

            $(this.options.elementId).controller().load_components();

        } else if (data.message == false) {

            show_error(lang.error);

        }


    },

    /*
     * CONNECT MENU ITEM
     */

    '.set_conect_menu_item submit': function(el, ev){
        ev.preventDefault();

        if ($('#menu_item_id').val() != '' || parseInt($('#menu_item_id').val()) > 0) {

            var data = $(el).serialize();

            Pages_model.set_conect_menu_item(data, this.callback('connectionSeted'));

        } else {

            alert('Введите пожалуйста название пункта меню!');

        }
    },

    connectionSeted: function(data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success == true) {

            show_success(lang.saved);

            $(this.options.elementId).controller().load_components();

        } else if (data.message == false)
            show_error(lang.error);
    },

    /*
     * HELPER functions
     */

    _validate: function(element) {
        $(element).validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 500
                },
                component_type_id: {
                    required: true,
                    number: true
                }
            },
            messages: {
                name: {
                    required: 'Это обязательное поле!',
                    minlength: 'Минимальное количество символов - 2',
                    maxlength: 'Максимальное количество символов - 500'
                },
                component_type_id: {
                    required: 'Это обязательное поле!',
                    number: 'Это числовое поле!'
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

    _autocomplete_menu_item: function() {
        $('#menu_item', this.elementId).autocomplete({
            serviceUrl: base_url+'admin/menu/menu_item_autocomplete', // Страница для обработки запросов автозаполнения
            minChars: 3, // Минимальная длина запроса для срабатывания автозаполнения
            //            delimiter: /(,|;)\s*/, // Разделитель для нескольких запросов, символ или регулярное выражение
            maxHeight: 400, // Максимальная высота списка подсказок, в пикселях
            width: 300, // Ширина списка
            zIndex: 19999, // z-index списка
            deferRequestBy: 300, // Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
            onSelect: function(data, value){
                $('#menu_item_id').attr('value', value);
            } // Callback функция, срабатывающая на выбор одного из предложенных вариантов,
        //            lookup: ['January', 'February', 'March'] // Список вариантов для локального автозаполнения
        });
    }

});