$.Controller.extend('Menu_item',{
    defaults: {
        viewpath:'//components/admin/menu_item/views/'
    }

},{
    init:function(){

        this.elementId = this.element.attr('id');

        if ($('#component_name', '#'+this.elementId).length) {
           this._autocomplete_component();
        }

        $('#menuItemDataForm').validate({
            rules:{
                position:{
                    maxlength: 3,
                    required:true
                }
            },
            messages: {
                position: {
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

    },

    /*
     * ---------------------------- Menu items save ----------------------------
     */

     '#menuItemDataForm submit': function(el, ev) {
         ev.preventDefault();

         $('#menuItemLangContent .content_lang:visible').find('.menuItemForm').submit();
     },

    '.menuItemForm submit': function(el,ev) {
        ev.preventDefault();
        var data,
            dataForm = $('#menuItemDataForm');

        this.validateMenuItem(el);

        if($(el).valid() == true && dataForm.valid() == true) {
            data = $(el).serialize()+'&'+dataForm.serialize();

            Menu_item_model.set_menu_item(data, this.callback('menuItemSaved', el));
        }

    },

    menuItemSaved: function(form, data) {

        if (data && data.message) {
            show_error(data.message);
        }

        if (data && data.success) {

            $('#menu_item_id').val(data.menu_item_id);

            $(form).find('.lang_id').val(data.menu_item_lang_id);

            show_success(lang.saved);

            if(this.options.full_functionality !== false){

                $(this.options.wrap).controller().loadMenuItems();

            }

        } else if (data.message == false) {
            show_error(lang.error);
        }



    },

    validateMenuItem: function(el) {

        $(el).validate({
            rules:{
                value:{
                    minlength: 2,
                    maxlength: 100,
                    required:true
                }
            },
            messages: {
                value: {
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

    /*
     * Component fuinctions
     */

    '.disconect_menu_item click': function(el){
        var menu_item_id = $('#menu_item_id').val();

        if (confirm('Вы действительно хотите отвязать пункт меню от страницы?')) {
            Menu_item_model.disconect_menu_item({
                menu_item_id: menu_item_id
            }, this.callback('disConectedMI'));
        }

    },

    disConectedMI: function(data){
        if (data.success == true) {

            if (data.message) {
                show_error(data.message);
                return;
            }

            $('#component_id').attr('val', '');
            $('.component_name').remove();
            $('.component_name_search').show();
            show_success('Изменения внесены успешно');

        } else show_error('Unknown error')
    },

    _autocomplete_component: function() {
        $('#component_name').autocomplete({
            serviceUrl: base_url+'admin/menu/component_autocomplete', // Страница для обработки запросов автозаполнения
            minChars: 3, // Минимальная длина запроса для срабатывания автозаполнения
            //            delimiter: /(,|;)\s*/, // Разделитель для нескольких запросов, символ или регулярное выражение
            maxHeight: 400, // Максимальная высота списка подсказок, в пикселях
            width: 300, // Ширина списка
            zIndex: 19999, // z-index списка
            deferRequestBy: 300, // Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
            onSelect: function(data, value){
                $('#component_id').attr('value', value.id);
                $('#component_type_id').attr('value', value.component_type_id);
            } // Callback функция, срабатывающая на выбор одного из предложенных вариантов,
        //            lookup: ['January', 'February', 'March'] // Список вариантов для локального автозаполнения
        });
    }

});