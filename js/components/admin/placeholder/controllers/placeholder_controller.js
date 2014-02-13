$.Controller('Placeholder', {
    defaults: {
        viewpath:'//components/admin/placeholder/views/'
    }
}, {
    /**
     * Placeholder form
     */
    init: function(element, data)
    {
        this.counter = 0;
        this.params = data;

        this.placeholderValidation();
    },

    placeholderValidation: function ()
    {
        $('#placeHolderForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 200
                },
                description: {
                    minlength: 2,
                    maxlength: 2000,
                    required: true
                },
                identificator: {
                    minlength: 3,
                    maxlength: 20,
                    required: true
                    /*
                     * TODO сделать метод для проверки уникальности идентификатора
                     */
                },
                position: {
                    maxlength: 3,
                    required: true
                },
                width: {
                    maxlength: 6
                },
                height: {
                    maxlength: 6
                },
                view: {
                    maxlength: 300
                }

            },
            messages: {

                name: {
                    required: 'Это обязательное поле',
                    minlength: 'Минимальное количество символов - 2',
                    maxlength: 'Максимальное количество символов - 200'
                },
                description: {
                    required: 'Это обязательное поле',
                    minlength: 'Минимальное количество символов - 2',
                    maxlength: 'Максимальное количество символов - 2000'
                },
                identificator: {
                    required: 'Это обязательное поле',
                    minlength: 'Минимальное количество символов - 2',
                    maxlength: 'Максимальное количество символов - 20'
                    /*
                     * TODO сделать метод для проверки уникальности идентификатора
                     */
                },
                position: {
                    required: 'Это обязательное поле',
                    maxlength: 'Максимальное количество символов - 3'
                },
                width: {
                    maxlength: 'Максимальное количество символов - 6'
                },
                height: {
                    maxlength: 'Максимальное количество символов - 6'
                },
                view: {
                    maxlength: 'Максимальное количество символов - 300'
                }
            },
            highlight: function(element) {
                $(element).parents('.control-group').addClass('error').find('.help').empty();
            },

            unhighlight: function(element) {
                $(element).parents('.control-group').removeClass('error').find('.help').empty();
            },

            errorPlacement: function(error, element) {
                //		var  id = '.'+element.attr('name')+'_error';
                //	alert($.dump(error));
                element.parents('.control-group').find('.help').html(error.html());
                //		//$(id).append(error);
            }

        });
    },

    '#placeHolderForm submit': function(el, ev)
    {
        ev.preventDefault();

        if (el.valid() !== false) {
            Placeholder_model.set_placeholder(el.serialize(), this.callback('placeholderSaved'));
        }
    },

    placeholderSaved: function(response)
    {
        if (response.success === true)
        {
            if (response.message) {
                show_error(response.message);
                return;
            }

            show_success(lang.placeholder.placeholderSaved);

            $('#placeholder_id').val(response.data);
            $('#set_placeholder_window .placeholderTab').removeClass('disabled');
            var callBackString = '$("#'+this.params.elementId+'").'+this.params.className+'("loadPlaceholders")';

            eval(callBackString);

        } else
        {
            show_error(lang.error);
        }
    },

    '.cancel click': function()
    {
        var selector = $('#set_placeholder_window, .modal-background');

        selector.fadeOut(300, function(){
            selector.remove();
        });
    },

    /*
     * Attribute
     */

    '#addPlaceholderAttribute click': function()
    {
        var html = $.View(this.Class.defaults.viewpath+'add_attribute.tmpl', {
            placeholder_id: $('#placeholder_id').val()
        });

        $('#set_placeholder_window .attributeForms').prepend(html);
    },

    '.saveAttr click': function(el)
    {
        var form = $(el).parents('.attributeForm');

        this.validateAttr(form);

        if (form.valid() !== false) {
            Placeholder_model.set_placeholder_attribute(form.serialize(), this.callback('attrSaved', form));
        }
    },

    attrSaved: function(form, response)
    {
        if (response.success === true)
        {
            if (response.message) {
                show_error(response.message);
                return;
            }

            form.find('.deleteAttr').children('span').removeClass('glyphicon-remove').addClass('glyphiconicon-trash');
            form.find('.attrId').val(response.data);

            show_success(lang.placeholder.placeholderSuccessfullyUpdated);

        } else
        {
            show_error(lang.error);
        }
    },

    '.deleteAttr click': function(el)
    {
        var $el = $(el);

        if ($el.children('span').hasClass('glyphicon-remove'))
        {
            $el.parents('.attributeForm').fadeOut(300, function(){
                $el.parents('.attributeForm').remove();
            });

        } else
        {
            if (confirm(lang.placeholder.removeAttributeConfirm)) {
                Placeholder_model.delete_placeholder_attribute($el.parents('.attributeForm').find('.attrId').val(), this.callback('attrDeleted', $el));
            }
        }
    },

    attrDeleted: function($el, response)
    {
        if (response.success === true)
        {
            if (response.message) {
                show_error(response.message);
                return;
            }

            show_success(lang.placeholder.attributeRemoved);

            $el.parents('.attributeForm').fadeOut(300, function(){
                $el.parents('.attributeForm').remove();
            });

        } else
            show_error(lang.error);
    },

    validateAttr: function(selector){
        selector.validate({
            rules: {
                key: {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                value: {
                    minlength: 2,
                    maxlength: 200,
                    required: true
                }
            },
            messages: {

                name: {
                    required: 'Это обязательное поле',
                    minlength: 'Минимальное количество символов - 2',
                    maxlength: 'Максимальное количество символов - 50'
                },
                description: {
                    required: 'Это обязательное поле',
                    minlength: 'Минимальное количество символов - 2',
                    maxlength: 'Максимальное количество символов - 200'
                }
            },
            highlight: function(element) {
                $(element).parents('.control-group').addClass('error').find('.help').empty();
            },

            unhighlight: function(element) {
                $(element).parents('.control-group').removeClass('error').find('.help').empty();
            },

            errorPlacement: function(error, element) {
                //		var  id = '.'+element.attr('name')+'_error';
                //	alert($.dump(error));
                element.parents('.control-group').find('.help').html(error.html());
                //		//$(id).append(error);
            }

        });
    },

    /*
     * Mini Block
     */

    '#addMiniBlock click': function(el)
    {
        var html = $.View(this.Class.defaults.viewpath+'add_mini_block.tmpl', {
            placeholder_id: $('#placeholder_id').val()
        });

        $('#set_placeholder_window .miniBlockPHContent').prepend(html);

        this._miniBlockAutoComplete($('#set_placeholder_window .miniBlockName:first'));
    },

    '.saveMiniBlock click': function(el)
    {
        var form = $(el).parents('.miniBlockForm');
        this.miniBlockValidate(form);

        if (form.valid() !== false)
        {
            Placeholder_model.set_mini_block(form.serialize(), this.callback('miniBlockSaved', form));
        }
    },

    miniBlockSaved: function(form, response)
    {
        if (response.success === true) {

            if (response.message) {
                show_error('Код ошибки: '+response.message);
                return;
            }

            form.find('.deleteMiniBlock').children('span').removeClass('glyphicon-remove').addClass('icon-trash');

            show_success('Изменения успешно внесены!');

        } else {

            show_error('Возникла неизвестная ошибка!');

        }
    },

    '.deleteMiniBlock click': function(el){

        var $el = $(el);

        if ($el.children('i').hasClass('icon-remove')) {

            $el.parents('.miniBlockForm').fadeOut(300, function(){
                $el.parents('.miniBlockForm').remove();
            });

        } else {
            if (confirm("Вы действительно хотите удалить связь с мини-блоком?")) {

                Placeholder_model.delete_miniblock({
                    mini_block_id: $el.parents('.miniBlockForm').find('.miniBlockId').val(),
                    placeholder_id: $('#placeholder_id').val()
                }, this.callback('miniBlockDeleted', $el));

            }
        }
    },

    miniBlockDeleted: function($el, response)
    {
        if (response.success === true)
        {
            if (response.message)
            {
                show_error(response.message);
                return;
            }

            show_success(lang.placeholder.miniBlockDeletedSuccess);

            $el.parents('.miniBlockForm').fadeOut(300, function(){
                $el.parents('.miniBlockForm').remove();
            });

        } else
            show_error(lang.error);
    },

    miniBlockValidate: function(form){
        form.validate({
            rules: {
                mini_block_id: {
                    required: true,
                    maxlength: 11
                }
            },
            messages: {
                mini_block_id: {
                    required: 'Это обязательное поле',
                    maxlength: 'Максимальное количество символов - 11'
                }
            },

            errorPlacement: function(error, element) {
                //		var  id = '.'+element.attr('name')+'_error';
                //	alert($.dump(error));
//                element.parents('.control-group').find('.help').html(error.html());
                alert(error.html());
                //		//$(id).append(error);
            }

        });
    },

    _miniBlockAutoComplete: function(el)
    {
        el.autocomplete({
            serviceUrl: base_url + 'admin/layout_controller/mini_block_autocomplete', // Страница для обработки запросов автозаполнения
            minChars: 2, // Минимальная длина запроса для срабатывания автозаполнения
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
    },

    /*
     * Product block
     */

    '#addProductBlock click': function()
    {
        var html = $.View(this.Class.defaults.viewpath+'add_product_block.tmpl', {
            counter: this.counter
        });

        $('#productBlockPHContent').prepend(html);

        this._productBlockAutocomplete($('#productBlock'+this.counter));

        this.counter++;
    },

    '.saveProductBlock click': function(el, ev)
    {
        ev.preventDefault();

        var productBlockForm = $(el).parents('.productBlockForm');

        if (parseInt(productBlockForm.find('.productBlockId').val()) !== 0)
        {
            Placeholder_model.set_product_block(productBlockForm.serialize()+'&placeholder_id='+$('#placeholder_id').val(), this.callback('productBlockSaved', productBlockForm));

        } else
        {
            alert(lang.placeholder.chooseProductBlock);
        }
    },

    productBlockSaved: function(form, data)
    {
        if (data.success === true)
        {
            if (data.message) {
                show_error('Код ошибки: '+data.message);
                return;
            }

            form.find('.deleteProductBlock').children('i').removeClass('icon-remove').addClass('icon-trash');
            form.find('.saveProductBlock').remove();
            form.find('.productBlockName').attr('disabled', true);

            show_success(lang.placeholder.productBlockRelationSaveSuccess);

        } else
        {
            show_error('Ой-ой что-то пошло не так(((');
        }
    },

    '.deleteProductBlock click': function(el) {
        var $el = $(el).parents('.productBlockForm');

        if ($(el).children('i').hasClass('icon-remove')) {

            $el.fadeOut(300, function(){
                $el.remove();
            });

        } else {
            if (confirm("Вы действительно хотите связь с блоком продуктов?")) {

                var data = {
                    placeholder_id: $('#placeholder_id').val(),
                    product_block_id: $el.find('.productBlockId').val()
                };

                Placeholder_model.delete_product_block(data, this.callback('productBlockDeleted', $el));

            }
        }
    },

    productBlockDeleted: function($el, data) {

        if (data.success === true) {

            if (data.message) {
                show_error('Код ошибки: '+data.message);
                return;
            }

            show_success('Связь с блоком товаров успешно удалена!');

            $el.fadeOut(300, function(){
                $el.remove();
            });

        } else {
            show_error('Ой-ой что-то пошло не так(((');
        }


    },

    _productBlockAutocomplete: function(el) {
        el.autocomplete({
            serviceUrl: base_url + 'admin/product_block_controller/product_block_autocomplete', // Страница для обработки запросов автозаполнения
            minChars: 2, // Минимальная длина запроса для срабатывания автозаполнения
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