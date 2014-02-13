$.Controller.extend('Menu_items',{

    defaults: {
        viewpath:'//components/admin/menu_item/views/',
        pref: getPref(),
        wind_opt: {
            width: findWindowWidth()
        }
    }

},{

    init:function(block, menu_block_id){

        this.menu_block_id = menu_block_id;

        this.elementId = this.element.attr('id');

        this.loadMenuItems();

    },

    /*
     * ----------------------------- Load MenuItems ----------------------------
     */

    loadMenuItems: function(){
        Menu_items_model.get_menu_item_by_block({
            menu_block_id: this.menu_block_id
        }, this.callback('menuItemsGeted'));

    },

    menuItemsGeted: function(data) {

        if (data && data.message) {

            show_error(data.message);

        }

        if (data){

            this.menu_item_data = data && data.data ? data.data : false;

            this.parent_data = [];
            this.i = 0;

            this.prepare_menu_item_data('0', 0);

            var html = $.View(this.options.viewpath+'get_menu_item.tmpl', {
                our_data: this.parent_data,
                lang: lang,
                menu_block_id: this.menu_block_id
            });

            $('#'+this.elementId).html(html);

        }

        componentLoaded(this.element);

    },

    prepare_menu_item_data: function(parent_id, lvl) {
        if (this.menu_item_data.length && this.menu_item_data !== false){
            var lvl_sign = '-', data = jlinq
            .from(this.menu_item_data)
            .equals("parent_id", parent_id)
            .sort('position')
            .select();

            lvl++;

            for (var j = 0; j < lvl-1; j++) {
                lvl_sign = lvl_sign+lvl_sign;
            }

            if (data.length) {
                for (var i = 0, ln = data.length; i < ln; i++) {
                    data[i].lvl = lvl;
                    data[i].lvl_sign = lvl_sign;
                    this.parent_data[this.i] = data[i];
                    this.i++;
                    this.prepare_menu_item_data(data[i].id, lvl);
                }
            }
        }
    },

    /*
     * ----------------------------- Set MenuItems ----------------------------
     */

    '.add_menu_item click': function(){

        this.setMenuItemCallback(false);

    },

    '.edit_menu_item click': function(el){

        var id = {
            menu_item_id: $(el).parents(".menu_item_wrap").attr('data-menu_item_id'),
            menu_block_id: this.menu_block_id
        };

        Menu_items_model.get_menu_item(id, this.callback('setMenuItemCallback'));
    },

    setMenuItemCallback: function(data) {

        if (data && data.message) {
            show_error(data.message);
            return;
        }

        var msg = data && data.data ? lang.menu_item.upd : lang.menu_item.add,
            html = $.View(this.options.viewpath+'setMenuItem.tmpl', {
                menu_block_id: this.menu_block_id,
                our_data: data ? data.data : false,
                pref: this.options.pref,
                parent: this.parent_data,
                msg: msg,
                component: component_types
            });

        loadWindow('menu_item', this.options.wind_opt, msg, html);

        $('#menu_item_window').menu_item({
            full_functionality: true,
            wrap: '#'+this.elementId,
            menu_item_id: data && data.data && typeof data.data != 'undefined' ? data.data.id : false
        });

    },

    /*
     * Delete menu item
     */

    '.delete_menu_item click': function(el){
        var id = {
            menu_item_id: $(el).parents(".menu_item_wrap").attr('data-menu_item_id')
        }, obj = this;

        if(confirm(lang.menu_item.conf_remove+'?')){
            Menu_items_model.delete_menu_item(id, function(data){
                obj.menuItemDeleted(data, el)
            });
        }
    },

    menuItemDeleted: function(data, el){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {

            var menu_item_id = $(el).parents(".menu_item_wrap").attr('data-menu_item_id'),
                obj = this;

            $('.header_menu_item[data-menu_item_id="'+menu_item_id+'"], .menu_item_wrap[data-menu_item_id="'+menu_item_id+'"]').fadeOut(300, function(){
                $('.header_menu_item[data-menu_item_id="'+menu_item_id+'"], .menu_item_wrap[data-menu_item_id="'+menu_item_id+'"]').remove();
                obj.loadMenuItems();
            });

            show_success(lang.removed);

        } else if (data.message == false) {

            show_error(lang.error);

        }

    },

    /*
     * Visible manipulation
     */

    '.header_menu_item mouseover': function(el) {
        var id = $(el).data('menu_item_id'),
        lvl = parseInt($(el).attr('data-lvl'))+1,
        html,
        child_menu_item_head = jlinq
        .from(this.menu_item_data)
        .equals("parent_id", id)
        .sort('position')
        .select();

        if (child_menu_item_head.length){
            if (!$(el).find('.sub_nav').length) {
                html = $.View(this.options.viewpath+'sub_nav_menu_item.tmpl', {
                    our_data: child_menu_item_head,
                    lvl: lvl,
                    site_url: base_url
                });

                $(el).append(html);

                $(el).find('a:first').next().fadeIn(300);
            } else {
                $(el).find('a:first').next().fadeIn(300);
            }

        }

    },

    '.header_menu_item mouseleave': function(el) {
        $(el).find('ul').stop(true, true).fadeOut(300);
    }

});