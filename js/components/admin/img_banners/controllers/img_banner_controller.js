$.Controller.extend('Img_banner',{
    defaults: {
        viewpath:'//components/admin/img_banners/views/',
        path_for_uploadify: 'js/resources/plugins/uploadify/',
        imgpath: 'uploads/images',
        upload_function: 'baner_images/set_img_banner'
    }

},{

    init:function(selector){

        this.elementId = '#'+this.element.attr('id');

        imperavi($('.content_desc', this.elementId));

        if (this.options.our_data) {

            this.upload_img($('#upload_img', this.elementId), this.options.our_data.id);

        }

        $('.bannerForm').validate({
            rules: {
                title: {
                    minlength: 2,
                    maxlength: 200,
                    required: true
                },
                description: {
                    minlength: 5,
                    maxlength: 3000,
                    required: true
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

        $('#bannerDataForm').validate({
            rules: {
                position: {
                    maxlength: 3,
                    required: true
                },
                top: {
                    maxlength: 5,
                    required: true
                },
                left: {
                    maxlength: 5,
                    required: true
                },
                width: {
                    maxlength: 5,
                    required: true
                },
                height: {
                    maxlength: 5,
                    required: true
                }
            },
            messages: {
                position: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                },
                top: {
                    required: lang.validation.required,
                    maxlength: lang.validation.maxlength
                },
                left: {
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

    '.bannerForm submit': function(el,ev){
        ev.preventDefault();
        var data,
            $dataForm = $('#bannerDataForm');

        if($(el).valid() == true && $dataForm.valid() == true) {
            data = $(el).serialize()+'&'+$dataForm.serialize();

            Img_banner_model.set_banner(data, this.callback('bannerSeted', el));
        }

    },

    bannerSeted: function($form, data){

        if (data.message) {
            show_error(data.message);
        }

        if (data && data.success) {
            show_success(lang.saved);

            $('#upload_wrapper').show();

            if (!$('#upload_imgUploader').length)
                this.upload_img($('#upload_img'), data.data.baner_id);

            $('#bannerDataForm .banner_id', this.element).val(data.data.baner_id);
            $($form).find('.banner_lang_id').val(data.data.baner_lang_id);

            $(this.options.elementId).controller().load_banner();

        } else if (data.message == false) {

            show_error(lang.error);

        }
    },

    '#delete_banner_img click': function(el){
        var img_id = {
                id: $(el).attr('data-img_id')
            };

        if(confirm(lang.banner.conf_remove_img+'?')){
            Img_banner_model.delete_banner_img(img_id, this.callback('imgDeleted', el));
        }
    },

    imgDeleted: function(el, data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {
            $(this.options.elementId).controller().load_banner();
            $(el).parent().fadeOut(300, function() {
                $(el).parent().children().remove();
            });
        } else if (data.message == false) {
            show_error(lang.error);
        }
    },

    upload_img: function(selector, id){
        var obj = this;

        selector.uploadify({
            'uploader': base_url+obj.options.path_for_uploadify+'uploadify.swf',
            'cancelImg': base_url+obj.options.path_for_uploadify+'cancel.png',
            'script': base_url+obj.options.upload_function,
            'scriptData': {
                banner_id: id
            },
            'removeCompleted' : true,
            'fileDesc': 'Select your image file',
            'fileExt': '*.png;*.jpg;*.gif;*.JPEG',
            //                'width':'200',
            'buttonText': 'Upload image',
            'multi': false,
            'auto': true,
            //                    'queueID':'upload_list_wrapper',
            'queueSizeLimit' : 5,
            //                'onAllComplete':function(){
            //                    $('.uploadifyQueueItem').remove();
            //                },
            'onComplete': function(event, ID, fileObj, response, data) {
                var resp = jQuery.parseJSON(response), selector;

                if (resp.message) {
                    show_error(resp.message);
                }

                if (resp.success) {
                    $('#banner_img').html('<img src="'+base_url+obj.options.imgpath+'/'+resp.data.name+'"/><div class="clear"></div><div class="btn btn-xs btn-danger" id="delete_banner_img" data-img_id="'+resp.data.id+'"><i class="glyphicon glyphicon-trash"/></div>').show();

                    $(obj.options.elementId).controller().load_banner();
                } else if (resp.message == false) {
                    show_error(lang.error);
                }

            },
            'onError': function(errorObj, data){
                show_error(lang.error);
            }
        });

    }

});