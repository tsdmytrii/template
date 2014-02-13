$.Controller.extend('Product_block',{
    defaults: {
        viewpath:'//components/admin/product_block/views/',
        lang_id: 2,
        pref: 'ru'
    }

},{
    init:function(selector, data){

        this.counter = 0;

        this.elementId = this.element.attr('id');

        this.params = data;

   },

    '.productBlockTab click': function(el, ev){
        ev.preventDefault();

        if (!el.hasClass('disabled'))
            tab_navigation(el, 'productBlockTab', 'active', '#formsWrap', 'cur', 'productBlockFormWrap', '#'+this.elementId, $(el).attr('href'));
    },

    '.productBlockLangTab click': function(el, ev){
        ev.preventDefault();

        tab_navigation(el, 'productBlockTab', 'active', '.productBlockLangWrap', 'current', 'content_lang', '#'+this.elementId, $(el).attr('href'));
    },

    '.cancel click': function(){
        var selector = $('#set_product_block_window, .modal-background');

        selector.fadeOut(300, function(){
            selector.remove();
        });
    },

    '.productBlockForm submit': function(el,ev){
        ev.preventDefault();
        var data;

        var productBlockInfoForm = $('#'+this.elementId+' .productBlockInfoForm');

        this.productBlockInfoValidate(productBlockInfoForm);
        this.productBlockValidate(el);

        if ($(el).valid() === true && productBlockInfoForm.valid() === true) {

            data = $(el).serialize()+'&'+productBlockInfoForm.serialize();

            Product_block_model.set_product_block(data, this.callback('productBlockSaved', el));

        }

    },

    productBlockSaved: function(el, data){
        if (data.success) {

             if (data.message) {
                show_error('Код ошибки: '+data.message);
                return;
            }

            show_success('Изменения внесены успешно');

            var callBackString = '$("#'+this.params.elementId+'").'+this.params.className+'("loadProductBlocks")';

            eval(callBackString);

            $('#product_block_id').val(data.data.product_block_id);
            el.find('.lang_id').val(data.data.lang_id);

        } else {
            show_error('Ошибка');
        }
    },

    productBlockValidate: function(element) {

        $(element).validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 200
                },
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                }
            },
            messages: {
                name: {
                    required: 'Это обязательное поле',
                    minlength: "Минимальное количество символов - 2",
                    maxlength: "Максимальное количество символов - 200"
                },
                description: {
                    required: 'Это обязательное поле',
                    minlength: "Минимальное количество символов - 2",
                    maxlength: "Максимальное количество символов - 1000"
                }
            },
            highlight: function(element){
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element){
                $(element).parents('.control-group').removeClass('error').find('.help').empty();
            },
            errorPlacement: function(error, element){
                element.next().append(error).animate({
                    opacity: "1"
                }, 1000);
            }
        });

    },

    productBlockInfoValidate: function(element){

        element.validate({
            rules: {
                row_quantity: {
                    required: true,
                    number: true,
                    maxlength: 3
                },
                column_quantity: {
                    required: true,
                    number: true,
                    maxlength: 2
                }
            },
            messages: {
                row_quantity: {
                    required: 'Это обязательное поле',
                    number: 'Введите пожалуйста число',
                    maxlength: "Максимальное количество символов - 3"
                },
                column_quantity: {
                    required: 'Это обязательное поле',
                    number: 'Введите пожалуйста число',
                    maxlength: "Максимальное количество символов - 2"
                }
            },
            highlight: function(element){
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element){
                $(element).parents('.control-group').removeClass('error').find('.help').empty();
            },
            errorPlacement: function(error, element){
                element.next().append(error).animate({
                    opacity: "1"
                }, 1000);
            }
        });
    },

    '#addRelProduct click': function() {

        var html = $.View(this.Class.defaults.viewpath+'add_product.tmpl', {
            counter: this.counter
        });

        $('#productsForm').prepend(html);

        this._autocomplete_product($('#productName'+this.counter));

        this.counter++;

    },

    '.saveProduct click': function(el) {

        var productForm = $(el).parents('.productForm');

        if (parseInt(productForm.find('.productId').val()) !== 0) {

            Product_block_model.set_product_relation(productForm.serialize()+'&product_block_id='+$('#product_block_id').val(), this.callback('productRelationSaved', productForm));

        } else {

            alert('Выберете пожалуйста товар');

        }

    },

    productRelationSaved: function(form, data) {

        if (data.success === true) {

             if (data.message) {
                show_error('Код ошибки: '+data.message);
                return;
            }

            form.find('.deleteProduct').children('i').removeClass('icon-remove').addClass('icon-trash');

            form.find('.saveProduct').remove();

            form.find('.productName').attr('disabled', true);

            show_success('Изменения успешно внесены!');

        } else {
            show_error('Ой-ой что-то пошло не так(((');
        }

    },

    '.deleteProduct click': function(el) {
        var $el = $(el).parents('.productForm');

        if ($(el).children('i').hasClass('icon-remove')) {

            $el.fadeOut(300, function(){
                $el.remove();
            });

        } else {
            if (confirm("Вы действительно хотите связь с продуктом?")) {

                var data = {
                    product_modification_id: $el.find('.productId').val(),
                    product_block_id: $('#product_block_id').val(),
                    related_field_id: $el.find('.relatedFieldId').val()
                };

                Product_block_model.delete_product_relation(data, this.callback('productDeleted', $el));

            }
        }
    },

    productDeleted: function($el, data) {

        if (data.success === true) {

             if (data.message) {
                show_error('Код ошибки: '+data.message);
                return;
            }

            show_success('Связь с товаром успешно удалена!');

            $el.fadeOut(300, function(){
                $el.remove();
            });

        } else {
            show_error('Ой-ой что-то пошло не так(((');
        }

    },

    _autocomplete_product: function(element){

        console.log($(element).next().val(value['$id']));

        element.autocomplete({
            serviceUrl: base_url + 'admin/product_controller/product_modification_autocomplete', // Страница для обработки запросов автозаполнения
            minChars: 2, // Минимальная длина запроса для срабатывания автозаполнения
            //            delimiter: /(,|;)\s*/, // Разделитель для нескольких запросов, символ или регулярное выражение
            maxHeight: 400, // Максимальная высота списка подсказок, в пикселях
            width: 300, // Ширина списка
            zIndex: 19999, // z-index списка
            deferRequestBy: 300, // Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
            onSelect: function(data, value, element) {

                $(element).next().val(value['$id']);

            } // Callback функция, срабатывающая на выбор одного из предложенных вариантов,
            //            lookup: ['January', 'February', 'March'] // Список вариантов для локального автозаполнения
        });
    }

});