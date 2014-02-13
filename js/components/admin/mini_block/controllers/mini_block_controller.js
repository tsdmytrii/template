$.Controller.extend('Mini_block',{
    defaults: {
        viewpath:'//components/admin/mini_block/views/',
        path_for_uploadify: 'js/resources/plugins/uploadify/',
        lang_id: 2,
        pref: 'ru'
    }

},{
    init:function(selector, data){

        this.elementId = $('.'+this.Class.fullName.toLowerCase()).attr('id');

        this.params = data;

        this._autocomplete_component();

        if (data.edit) {

            this.upload_logo();
            this.uploadToolTip();

            var html = false;

            switch(parseInt(data.component_type_id)) {
                case 1:
                    html = $.View(this.Class.defaults.viewpath+'static_comp_behavior.tmpl', {
                        our_data: data.data
                    });
                    break;
                case 7:
                    html = $.View(this.Class.defaults.viewpath+'article_behavior.tmpl', {
                        our_data: data.data
                    });
                    break;
            }

            if (html !== false)
                $('.contentBehaviorForm', '#'+this.elementId).html(html).parents('table').show();

        }


    },

    '.miniBlockTab click': function(el, ev){
        ev.preventDefault();

        tab_navigation(el, 'miniBlockTab', 'active', '.miniBlockLangWrap', 'current', 'content_lang', '#'+this.elementId, $(el).attr('href'));
    },

    '.cancel click': function(){
        var selector = $('#set_mini_block_window, .modal-background');

        selector.fadeOut(300, function(){
            selector.remove();
        });
    },

    '.miniBlockForm submit': function(el,ev){
        ev.preventDefault();
        var data;

        this.miniBlockInfoValidate($('#'+this.elementId+' .miniBlockInfoForm'));
        this.miniBlockValidate(el);

        if ($(el).valid() === true && $('#'+this.elementId+' .miniBlockInfoForm').valid() === true) {

            data = $(el).serialize()+'&'+$('#'+this.elementId+' .miniBlockInfoForm').serialize();

            Mini_block_model.set_mini_block(data, this.callback('miniBlockSaved'));

        }

    },

    miniBlockSaved: function(data, el){
        if (data.success) {

            if (data.message) {
                show_error('Код ошибки: '+data.message);
                return;
            }

            show_success('Изменения внесены успешно');

            var callBackString = '$("#'+this.params.elementId+'").'+this.params.className+'("loadMiniBlocks")';

            eval(callBackString);

            $('.useBgWrap, .tooltipImg').show();

            this.upload_logo();

            this.uploadToolTip();

        } else {
            show_error('Ошибка');
        }
    },

    /*
     * Logo - images functions
     */

    '.miniBlockImg click': function(el){
        if ($(el).is(':checked')) {

            $('#'+this.elementId+' .bgImg').show();

        } else {

            $('#'+this.elementId+' .bgImg').hide();

        }
    },

    upload_logo: function(){

        this.upload_function('#img_upload', 'logo_controller/set_mini_block_img', 'logo');

    },

    logoUploaded: function(data){

        $('#'+this.elementId+' .miniBlockImgWrap').show().html('<img src="'+base_url+'uploads/images/'+data.name+'"><div class="clear"></div><i style="margin: 5px auto;" class="icon-trash menu_icon delete_img" data-mini_block_img_id="'+data.id+'" title="Удалить картинку"></i><div class="clear"></div>');

    },

    '.delete_img click': function(el){
        var id = $(el).attr('data-mini_block_img_id'),
        obj = this;

        if(confirm('Вы действительно хотите удалить эту картинку?')){
            Mini_block_model.delete_img({
                id: id
            }, function(data){
                obj.imgDeleted(data, el);
            }, obj.callback('error'));
        }
    },

    imgDeleted: function(data, el) {

        if (data.success == true) {

            show_success('Картинка удалена!');

            $(el).parents('.miniBlockImgWrap').fadeOut(300, function(){
                $(el).parents('.miniBlockImgWrap').empty();
            });

        } else show_error('Ошибка!');

    },

    /*
     * Toltip functions
     */

    uploadToolTip: function(){

        this.upload_function('#tooltip_upload', 'logo_controller/set_mini_block_tooltip', 'tooltip');

    },

    toolTipUploaded: function(data){

        console.log(data);

        $('#'+this.elementId+' .miniBlockToolTipWrap').show().html('<img src="'+base_url+'uploads/images/'+data.name+'"><div class="clear"></div><i style="margin: 5px auto;" class="icon-trash menu_icon deleteToolTip" data-tooltip_id="'+data.id+'" title="Удалить картинку"></i><div class="clear"></div>');

    },

    '.deleteToolTip click': function(el){
        var id = $(el).attr('data-tooltip_id'),
        obj = this;

        if(confirm('Вы действительно хотите удалить эту картинку?')){
            Mini_block_model.delete_tooltip({
                id: id
            }, this.callback('toolTipDeleted', el));
        }
    },

    toolTipDeleted: function(el, data) {

        if (data.success == true) {

            show_success('Картинка удалена!');

            $(el).parents('.miniBlockToolTipWrap').fadeOut(300, function(){
                $(el).parents('.miniBlockToolTipWrap').empty();
            });

        } else show_error('Ошибка!');

    },

    /*
     * Upload function
     */

    upload_function: function(selector, upload_method, method){
        var obj = this;

        $(selector).uploadify({
            'uploader': base_url+obj.Class.defaults.path_for_uploadify+'uploadify.swf',
            'cancelImg': base_url+obj.Class.defaults.path_for_uploadify+'cancel.png',
            'script': base_url+upload_method,
            'scriptData':{
                mini_block_id: $('#'+obj.elementId+' .mini_block_id').val()
            },
            'removeCompleted' : true,
            'fileDesc': 'Select your image file',
            'fileExt': '*.*',
            'buttonText': 'Upload',
            'multi': false,
            'auto': true,
            'queueSizeLimit' : 5,
            'onAllComplete':function(){
//                $('.uploadifyQueueItem').remove();
            },
            'onComplete': function(event, ID, fileObj, response, data) {
                var resp = jQuery.parseJSON(response), selector;
                resp = resp.data;

                if (method == 'logo')
                    obj.logoUploaded(resp);
                else
                    obj.toolTipUploaded(resp);

            },
            'onError': function(errorObj, data){
                throw_message(errorObj, 'error');
            }
        });
    },

    /*
     * Validation functions
     */

    miniBlockValidate: function(element)
    {
        $(element).validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 200
                },
                button_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 200
                }
            },
            messages: {
                name: {
                    required: 'Это обязательное поле',
                    minlength: "Минимальное количество символов - 2",
                    maxlength: "Максимальное количество символов - 200"
                },
                button_name: {
                    required: 'Это обязательное поле',
                    minlength: "Минимальное количество символов - 2",
                    maxlength: "Максимальное количество символов - 200"
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

    miniBlockInfoValidate: function(element)
    {
        $(element).validate({
            rules: {
                place: {
                    required: true,
                    minlength: 1,
                    maxlength: 11
                }
            },
            messages: {
                place: {
                    required: 'Это обязательное поле',
                    minlength: "Минимальное количество символов - 2",
                    maxlength: "Максимальное количество символов - 11"
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

    _autocomplete_component: function()
    {
        var obj = this;

        $('#component_name', '#'+this.elementId).autocomplete({
            serviceUrl: base_url+'admin/components/component_autocomplete_mini_block', // Страница для обработки запросов автозаполнения
            minChars: 3, // Минимальная длина запроса для срабатывания автозаполнения
            //            delimiter: /(,|;)\s*/, // Разделитель для нескольких запросов, символ или регулярное выражение
            maxHeight: 400, // Максимальная высота списка подсказок, в пикселях
            width: 300, // Ширина списка
            zIndex: 19999, // z-index списка
            deferRequestBy: 300, // Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
            onSelect: function(data, value){

                $('#'+obj.elementId+' .component_id').val(value.id);
                $('#'+obj.elementId+' .content_id').val(value.content_id);

                var html = $.View(obj.Class.defaults.viewpath + value.component_type_id + '.tmpl', {});

                $('.contentBehaviorForm', '#'+obj.elementId).html(html).parents('table').show();

            } // Callback функция, срабатывающая на выбор одного из предложенных вариантов,
        //            lookup: ['January', 'February', 'March'] // Список вариантов для локального автозаполнения
        });
    }

});