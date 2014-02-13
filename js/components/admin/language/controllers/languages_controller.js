$.Controller.extend('Languages',{
    defaults: {
        viewpath:'//components/admin/language/views/',
        wind_opt: {
            width: findWindowWidth()
        }
    }

},{
    init:function(selector) {

        this.elementId = this.element.attr('id');

        this.load_languages();

    },

    /*
     * Get lenguages
     */

    load_languages: function(){

        Languages_model.get_all_languages(this.callback('languagesLoaded'));

    },

    languagesLoaded: function(data){

        if (data && data.message) {
            show_error(data.message)
        }

        if (data && !data.message)
            langs = data.data;

        if (data && data.success) {
            var html = $.View(this.Class.defaults.viewpath+'all_languages.tmpl', {
                our_data: data
            });

            $('#'+this.elementId).html(html);
            
        } else if (data.message == false) {
            show_error(lang.error);
        }

        componentLoaded(this.element);

    },

    /*
     * Set languages
     */

    '.add_language click': function(){

        this.setLanguageCallback(false);

    },

    '.edit_language click': function(el){
        var id = {
            language_id: $(el).parents(".language_icon_wrap").data('language_id')
        };

        Languages_model.get_language(id, this.callback('setLanguageCallback'));
    },

    setLanguageCallback: function(response){

        if (response && response.message) {

            show_error(response.message);

            return;
        }

        var msg = response ? lang.lang.upd : lang.lang.add;

        loadWindow('language', this.Class.defaults.wind_opt,  msg, $.View(this.Class.defaults.viewpath+'set_language.tmpl', {

            site_url: base_url,
            our_data: response ? response.data : false

        }));

        $('#language_window').language({
            full_functionality:true,
            language_wrap: '#'+this.elementId,
            language_id: response && response.data && typeof response.data != undefined ? response.data.id : false
        });

    },

    /*
     * Delete language
     */

    '.delete_language click': function(el){
        var id = {
            language_id: $(el).parents(".language_icon_wrap").data('language_id')
        };

        if(confirm(lang.lang.conf_remove+'?')){
            Languages_model.delete_language(id, this.callback('languageDeleted', el));
        }
    },

    languageDeleted: function(el, data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success == true) {

            show_success(lang.removed);

            el.parents('tr').slideUp(300, function(){
                el.parents('tr').remove();
            });

        } else if (data.message == false) {
            show_error(lang.error);
        }
    }

});