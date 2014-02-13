$.Model('Menu_model', {

    get_menu_block: function(data, success) {
        $.ajax({
            url: base_url+'admin/menu/get_menu_block',
            data: data,
            success: this.callback(success)
        });
    },

    get_all_menu_blocks: function(success){
        $.ajax({
            url: base_url+'admin/menu/get_all_menu_blocks',
            success: this.callback(success)
        });
    },

    delete_menu_block: function(data, success){
        $.ajax({
            url: base_url+'admin/menu/delete_menu_block',
            data: data,
            success: this.callback(success)
        });
    }

}, {});