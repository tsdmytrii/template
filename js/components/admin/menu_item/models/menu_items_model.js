$.Model('Menu_items_model', {

    get_menu_item_by_block: function(data, success){
        $.ajax({
            url: base_url+'admin/menu/get_menu_item_by_block',
            data: data,
            success: this.callback(success)
        });
    },

    get_menu_item: function(data, success){
        $.ajax({
            url: base_url+'admin/menu/get_menu_item',
            data: data,
            success: this.callback(success)
        });
    },

    delete_menu_item: function(data, success){
        $.ajax({
            url: base_url+'admin/menu/delete_menu_item',
            data: data,
            success: this.callback(success)
        });
    }

}, {});