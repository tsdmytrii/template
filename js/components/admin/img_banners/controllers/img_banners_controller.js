$.Controller.extend('Img_banners',{
    defaults: {
        viewpath:'//components/admin/img_banners/views/',
        imgpath: 'uploads/images',
        pref: getPref(),
        wind_opt: {
            width: findWindowWidth()
        }
    }

},{

    init:function(selector){

        var html = $.View(this.options.viewpath+'index.tmpl', {});

        this.elementId = '#'+this.element.attr('id');

        this.element.html(html);

        this.load_banner();
    },

    /*
     * ------------------------------------------------------------------------- Get banners functions
     */

    load_banner: function(){

        Img_banners_model.get_all_banners(this.callback('bannersGeted'));

    },

    bannersGeted: function(data){

        if (data && data.message) {
            show_error(data.message);
        }

        if (data && data.success) {

            var html = $.View(this.options.viewpath+'all_banner.tmpl', {
                our_data: data.data,
                lang: lang,
                directory: this.options.imgpath,
                pref: this.options.pref
            });

            $('#bannersWrap').html(html);
        } else if (data && data.message == false) {
            show_error(lang.error);
        }

        componentLoaded(this.element);
    },

    /*
     * ------------------------------------------------------------------------- Set banners functions
     */

    '#add_banner click': function(el){

        this.setImgBanerCallback(false);

    },

    '.editBanner click': function(el) {
        var id = {
            banner_id: $(el).parents(".banner_icon_wrap").attr('data-banner_id')
        };

        Img_banners_model.get_banner(id, this.callback('setImgBanerCallback'));
    },

    setImgBanerCallback: function(data) {

        if (data && data.message) {
            show_error(data.message);
            return;
        }

        var html = $.View(this.options.viewpath+'set_banner.tmpl', {
                our_data: data && data.data ? data.data : false,
                directory: this.options.imgpath
            }),
            msg = data && data.data && typeof data.data != 'undefined' ? lang.banner.upd : lang.banner.add;

        loadWindow('banner', this.options.wind_opt, msg, html);

        $('#banner_window').img_banner({
            full_functionality:true,
            elementId: this.elementId,
            our_data: data && data.data ? data.data : false
        });

    },

    /*
     * ------------------------------------------------------------------------- DELETE banners functions
     */

    '.deleteBanner click': function(el) {
        var id = {
                banner_id: $(el).parents(".banner_icon_wrap").attr('data-banner_id')
            };

        if(confirm(lang.banner.conf_remove+'?')){
            Img_banners_model.delete_banner(id, this.callback('banerDeleted', el));
        }
    },

    banerDeleted: function(el, data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {

            el.parents('tr').fadeOut(300, function(){
                el.parents('tr').remove();
            });

            show_success(lang.removed);
        } else if (data.message == false) {
            show_error(lang.error);
        }
    },

    '.delete_banner_img click': function(el){
        var img_id = {
                id: $(el).attr('data-img_id')
            };

        if(confirm(lang.banner.conf_remove_img+'?')){

            Img_banners_model.delete_banner_img(img_id, this.callback('imgDeleted', el));

        }
    },

    imgDeleted: function(el, data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {
            $(el).parents('td').children().fadeOut(300, function(){
                $(el).parents('td').children().remove();
            });
        } else if (data.message == false) {
            show_error(lang.error);
        }
    }

});