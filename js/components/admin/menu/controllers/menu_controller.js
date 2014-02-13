$.Controller.extend('Menu',{
    defaults: {
        viewpath:'//components/admin/menu/views/',
        wind_opt: {
            width: findWindowWidth()
        }
    }

},{
    init:function(){

        this.elementId = this.element.attr('id');

        var html = $.View(this.Class.defaults.viewpath+'index.tmpl', {});

        this.element.html(html);

        this.loadMenuBlock();

    },

    /*
     * Load menu block
     */

    loadMenuBlock: function(id){

        Menu_model.get_all_menu_blocks(this.callback('menuBlocksGeted'));

    },

    menuBlocksGeted: function(data){

        if (data && data.message) {

            show_error(lang.errorCode+data.message);

        } else {

            var html = $.View(this.Class.defaults.viewpath+'get_menu_block.tmpl', {
                our_data: data.data,
                base_url: base_url
            });

            $('#menuBlockContent').html(html);

        }

        componentLoaded(this.element);

    },

    /*
     * SET menu block
     */

    '#addMenuBlock click': function(){

        this.setMenuBlockCallback(false);

    },

    '.editMenuBlock click': function(el){
        var id = {
            menu_block_id: $(el).parents(".menu_block_icon_wrap").data('menu_block_id')
        };

        Menu_model.get_menu_block(id, this.callback('setMenuBlockCallback'));
    },

    setMenuBlockCallback: function(data){

        if (data && data.message) {
            show_error(lang.errorCode+data.message);
            return;
        }

        var html = $.View(this.Class.defaults.viewpath+'set_menu_block.tmpl', {
                our_data: data ? data.data : false
            }),
            msg = data ? lang.menu.upd : lang.menu.add;

//        this.options.wind_opt.width = ;

        loadWindow('set_menu_block', this.options.wind_opt, msg, html);

        $('#set_menu_block_window').menu_block({
            data: data ? data.data : false,
            elementId: this.elementId
        });

    },

    /*
     * DELETE menu block
     */

    '.deleteMenuBlock click': function(el){
        var id = {
            menu_block_id: $(el).parents(".menu_block_icon_wrap").data('menu_block_id')
        };

        if(confirm(lang.menu.conf_delete+'?')){
            Menu_model.delete_menu_block(id, this.callback('menuBlockDeleted', el));
        }
    },

    menuBlockDeleted: function(el, data){

        if (data.success) {

            if (data.message) {
                show_error(lang.errorCode+data.message);
                return;
            }

            show_success('Операция удаления успешна!');

            var tr = el.parents('tr');

            tr.slideUp(300, function(){
                tr.remove();
            });

        } else
            show_error('Ошибка');


    }

}
);