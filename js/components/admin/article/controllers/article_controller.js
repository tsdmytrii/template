$.Controller.extend('Article',{
    defaults: {
        viewpath:'//components/admin/article/views/',
        path_for_uploadify: 'js/resources/plugins/uploadify/',
        upload_function: 'picture_controller/set_article_item_image',
        pref: getPref()
    }

},{
    init:function(selector){

        this.elementId = this.element.attr('id');

        imperavi($('.content_desc', '#'+this.elementId));

        date_time_picker($('#date_time'));

        for (var i = 0, length = langs.length; i < length; i++) {

            $('#name_'+langs[i].iso_code).syncTranslit({destination: this.elementId+' #link_'+langs[i].iso_code, urlSeparator: '_'});

        }

        $('#articleItemForm').validate({
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

        $('#articleItemInfoForm').validate({
            rules: {
                date_time: {
                    required: true,
                    minlength: 8,
                    maxlength: 19
                }
            },
            messages: {
                date_time: {
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

    '.articleItemForm submit': function(el,ev){
        ev.preventDefault();
        var data,
            articleItemInfoForm = $('#articleItemInfoForm');

        if($(el).valid() == true && articleItemInfoForm.valid() == true) {

            data = $(el).serialize()+'&'+articleItemInfoForm.serialize();

            Article_model.set_article(data, this.callback('articleSaved', el));

        }

    },

    articleSaved: function(el, data) {
        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {


            show_success(lang.saved);

            $('#article_item_id').val(data.data.article_item_id);

            $(el).find('.lang_id').val(data.data.article_item_lang_id);

            if (data.data.link_id)
                $(el).find('.link_id').val(data.data.link_id);

            $('#'+this.options.elementId).controller().loadArticles();

        } else if (data.message !== false) {
            show_error(lang.error);
        }
    }

});